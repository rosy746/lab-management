<?php

namespace App\Services;

use App\Models\Finance\WaSetting;
use App\Models\Finance\WaNotificationLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private ?WaSetting $setting;

    private string $baileysUrl;
    private string $baileysKey;

    public function __construct()
    {
        $this->setting    = WaSetting::where('is_active', true)->first();
        $this->baileysUrl = config('services.baileys.url', 'http://localhost:3001');
        $this->baileysKey = config('services.baileys.key', env('BAILEYS_API_KEY', ''));
    }

    /* ─────────────────────────────────────────────────────
     | HELPER: deteksi apakah target adalah grup
     ───────────────────────────────────────────────────── */
    private function isGroup(string $target): bool
    {
        // Format grup Baileys: 120363xxxxxxxx@g.us
        return str_contains($target, '@g.us');
    }

    /* ─────────────────────────────────────────────────────
     | HELPER: normalisasi target (nomor HP atau grup ID)
     ───────────────────────────────────────────────────── */
    private function normalizeTarget(string $target): string
    {
        $target = trim($target);

        // Jika grup, kembalikan apa adanya
        if ($this->isGroup($target)) {
            return $target;
        }

        // Nomor HP: pastikan format internasional tanpa +
        $target = preg_replace('/\D/', '', $target); // hapus non-digit
        if (str_starts_with($target, '0')) {
            $target = '62' . substr($target, 1);
        }
        if (!str_starts_with($target, '62')) {
            $target = '62' . $target;
        }

        return $target;
    }

    /* ─────────────────────────────────────────────────────
     | PUBLIC: kirim ke SATU target
     ───────────────────────────────────────────────────── */
    public function send(string $phone, string $message, ?int $transactionId = null): bool
    {
        if (!$this->setting || !$this->setting->is_active) {
            $this->log($phone, $message, $transactionId, 'failed', 'WA setting tidak aktif');
            return false;
        }

        return $this->dispatch($phone, $message, $transactionId);
    }

    /* ─────────────────────────────────────────────────────
     | PUBLIC: kirim ke SEMUA target terdaftar
     ───────────────────────────────────────────────────── */
    public function sendToAll(string $message, ?int $transactionId = null): array
    {
        if (!$this->setting || !$this->setting->is_active) {
            return ['success' => 0, 'failed' => 0, 'targets' => []];
        }

        $targets = $this->setting->getAllTargets();
        $results = ['success' => 0, 'failed' => 0, 'targets' => []];

        if (empty($targets)) return $results;

        if (count($targets) > 1) {
            return $this->dispatchBulk($targets, $message, $transactionId);
        }

        // Single target
        $ok = $this->dispatch($targets[0], $message, $transactionId);
        $results[$ok ? 'success' : 'failed']++;
        $results['targets'][] = ['phone' => $targets[0], 'ok' => $ok];

        return $results;
    }

    /* ─────────────────────────────────────────────────────
     | NOTIFY HELPERS
     ───────────────────────────────────────────────────── */
    public function notifyIncome(array $data): bool
    {
        $flag = $this->setting?->notify_income ?? $this->setting?->notify_on_transaction ?? false;
        if (!$flag) return false;

        $result = $this->sendToAll($this->buildIncomeMessage($data), $data['transaction_id'] ?? null);
        return $result['success'] > 0;
    }

    public function notifyExpense(array $data): bool
    {
        $flag = $this->setting?->notify_expense ?? $this->setting?->notify_on_transaction ?? false;
        if (!$flag) return false;

        $result = $this->sendToAll($this->buildExpenseMessage($data), $data['transaction_id'] ?? null);
        return $result['success'] > 0;
    }

    public function notifyBudgetWarning(array $data): bool
    {
        $flag = $this->setting?->notify_budget_warning ?? $this->setting?->notify_on_budget_exceeded ?? false;
        if (!$flag) return false;

        $result = $this->sendToAll($this->buildBudgetWarningMessage($data));
        return $result['success'] > 0;
    }

    public function shouldWarnBudget(float $used, float $amount): bool
    {
        if (!$this->setting || $amount == 0) return false;
        $threshold = $this->setting->budget_warning_pct
                  ?? $this->setting->budget_alert_threshold
                  ?? 80;
        return (($used / $amount) * 100) >= $threshold;
    }

    /* ─────────────────────────────────────────────────────
     | INTERNAL: dispatch ke Baileys API (single)
     ───────────────────────────────────────────────────── */
    private function dispatch(string $phone, string $message, ?int $transactionId = null): bool
    {
        // Normalisasi target sebelum dikirim
        $normalizedTarget = $this->normalizeTarget($phone);
        $isGroup          = $this->isGroup($normalizedTarget);

        $logEntry = $this->log($phone, $message, $transactionId, 'pending', null);

        try {
            // Payload berbeda untuk grup vs nomor HP
            $payload = [
                'message' => $message,
            ];

            if ($isGroup) {
                // Baileys pakai 'groupId' untuk kirim ke grup
                $payload['groupId'] = $normalizedTarget;
            } else {
                $payload['target'] = $normalizedTarget;
            }

            $response = Http::timeout(10)
                ->withHeaders(['X-API-Key' => $this->baileysKey])
                ->post("{$this->baileysUrl}/send", $payload);

            $data    = $response->json();
            $success = $response->successful() && ($data['success'] ?? false);

            // Beberapa Baileys API return 'status' => 'sent' bukan 'success' => true
            if (!$success && $response->successful()) {
                $success = ($data['status'] ?? '') === 'sent'
                        || ($data['status'] ?? '') === 'success'
                        || isset($data['messageId'])
                        || isset($data['id']);
            }

            $logEntry?->update([
                'status'   => $success ? 'sent' : 'failed',
                'response' => json_encode($data),
                'sent_at'  => $success ? now() : null,
            ]);

            if (!$success) {
                Log::warning("Baileys send failed to [{$normalizedTarget}]", ['response' => $data]);
            }

            return $success;

        } catch (\Exception $e) {
            Log::error('Baileys send error: ' . $e->getMessage(), [
                'target' => $normalizedTarget,
                'isGroup' => $isGroup,
            ]);
            $logEntry?->update(['status' => 'failed', 'response' => $e->getMessage()]);
            return false;
        }
    }

    /* ─────────────────────────────────────────────────────
     | INTERNAL: dispatch bulk ke Baileys API
     ───────────────────────────────────────────────────── */
    private function dispatchBulk(array $targets, string $message, ?int $transactionId = null): array
    {
        $results = ['success' => 0, 'failed' => 0, 'targets' => []];

        // Pisahkan target grup dan nomor HP
        $phones = [];
        $groups = [];

        foreach ($targets as $t) {
            $normalized = $this->normalizeTarget($t);
            if ($this->isGroup($normalized)) {
                $groups[] = $normalized;
            } else {
                $phones[] = $normalized;
            }
        }

        // Kirim ke nomor HP via bulk endpoint (jika ada)
        if (!empty($phones)) {
            try {
                $response = Http::timeout(30)
                    ->withHeaders(['X-API-Key' => $this->baileysKey])
                    ->post("{$this->baileysUrl}/send-bulk", [
                        'targets' => $phones,
                        'message' => $message,
                    ]);

                $data = $response->json();

                if ($response->successful() && isset($data['results'])) {
                    foreach ($data['results'] as $r) {
                        $ok = $r['ok'] ?? $r['success'] ?? false;
                        $results[$ok ? 'success' : 'failed']++;
                        $results['targets'][] = ['phone' => $r['target'], 'ok' => $ok];
                        $this->log($r['target'], $message, $transactionId, $ok ? 'sent' : 'failed', $ok ? null : ($r['error'] ?? 'unknown'));
                    }
                } else {
                    // Fallback: kirim satu-satu
                    foreach ($phones as $p) {
                        $ok = $this->dispatch($p, $message, $transactionId);
                        $results[$ok ? 'success' : 'failed']++;
                        $results['targets'][] = ['phone' => $p, 'ok' => $ok];
                    }
                }

            } catch (\Exception $e) {
                Log::error('Baileys bulk error: ' . $e->getMessage());
                foreach ($phones as $p) {
                    $ok = $this->dispatch($p, $message, $transactionId);
                    $results[$ok ? 'success' : 'failed']++;
                    $results['targets'][] = ['phone' => $p, 'ok' => $ok];
                }
            }
        }

        // Kirim ke grup satu-satu (bulk grup tidak disupport Baileys)
        foreach ($groups as $g) {
            $ok = $this->dispatch($g, $message, $transactionId);
            $results[$ok ? 'success' : 'failed']++;
            $results['targets'][] = ['phone' => $g, 'ok' => $ok];
        }

        return $results;
    }

    /* ─────────────────────────────────────────────────────
     | LOG
     ───────────────────────────────────────────────────── */
    private function log(
        string  $phone,
        string  $message,
        ?int    $transactionId,
        string  $status,
        ?string $response
    ): ?WaNotificationLog {
        try {
            return WaNotificationLog::create([
                'transaction_id' => $transactionId,
                'phone'          => $phone,
                'message'        => $message,
                'status'         => $status,
                'response'       => $response,
                'created_at'     => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('WaNotificationLog create failed: ' . $e->getMessage());
            return null;
        }
    }

    /* ─────────────────────────────────────────────────────
     | MESSAGE BUILDERS
     ───────────────────────────────────────────────────── */
    private function buildIncomeMessage(array $data): string
    {
        return implode("\n", [
            '✅ *PEMASUKAN BARU*',
            '━━━━━━━━━━━━━━━━━━━━',
            "📋 Kode     : {$data['code']}",
            "📂 Kategori : {$data['category']}",
            '💰 Jumlah   : Rp ' . number_format($data['amount'], 0, ',', '.'),
            "📅 Tanggal  : {$data['date']}",
            "📝 Ket      : {$data['description']}",
            "👤 Dicatat  : {$data['created_by']}",
            '━━━━━━━━━━━━━━━━━━━━',
            '💳 Saldo Kas: Rp ' . number_format($data['balance'], 0, ',', '.'),
        ]);
    }

    private function buildExpenseMessage(array $data): string
    {
        $lines = [
            '🔴 *PENGELUARAN BARU*',
            '━━━━━━━━━━━━━━━━━━━━',
            "📋 Kode     : {$data['code']}",
            "📂 Kategori : {$data['category']}",
            '💸 Jumlah   : Rp ' . number_format($data['amount'], 0, ',', '.'),
            "📅 Tanggal  : {$data['date']}",
            "📝 Ket      : {$data['description']}",
            "👤 Dicatat  : {$data['created_by']}",
        ];

        if (!empty($data['budget_amount'])) {
            $pct     = round(($data['budget_used'] / $data['budget_amount']) * 100);
            $lines[] = '📊 Anggaran : Rp ' . number_format($data['budget_amount'], 0, ',', '.') . " (terpakai {$pct}%)";
        }

        $lines[] = '━━━━━━━━━━━━━━━━━━━━';
        $lines[] = '💳 Saldo Kas: Rp ' . number_format($data['balance'], 0, ',', '.');

        return implode("\n", $lines);
    }

    private function buildBudgetWarningMessage(array $data): string
    {
        $pct = round(($data['used'] / $data['amount']) * 100);
        return implode("\n", [
            '⚠️ *PERINGATAN ANGGARAN*',
            '━━━━━━━━━━━━━━━━━━━━',
            "📂 Kategori : {$data['category']}",
            "📅 Periode  : {$data['period']}",
            "📊 Terpakai : {$pct}% dari anggaran",
            '💰 Anggaran : Rp ' . number_format($data['amount'], 0, ',', '.'),
            '💸 Terpakai : Rp ' . number_format($data['used'], 0, ',', '.'),
            '🔖 Sisa     : Rp ' . number_format($data['remaining'], 0, ',', '.'),
            '━━━━━━━━━━━━━━━━━━━━',
            'Segera review pengeluaran kategori ini.',
        ]);
    }
}