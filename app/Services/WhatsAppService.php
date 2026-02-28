<?php

namespace App\Services;

use App\Models\Finance\WaSetting;
use App\Models\Finance\WaNotificationLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private ?WaSetting $setting;

    // URL Baileys API — sesuaikan jika port berbeda
    private string $baileysUrl;
    private string $baileysKey;

    public function __construct()
    {
        $this->setting    = WaSetting::where('is_active', true)->first();
        $this->baileysUrl = config('services.baileys.url', 'http://localhost:3001');
        $this->baileysKey = config('services.baileys.key', env('BAILEYS_API_KEY', ''));
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

        // Coba bulk send dulu (lebih efisien)
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
     | INTERNAL: dispatch ke Baileys API
     ───────────────────────────────────────────────────── */
    private function dispatch(string $phone, string $message, ?int $transactionId = null): bool
    {
        $logEntry = $this->log($phone, $message, $transactionId, 'pending', null);

        try {
            $response = Http::timeout(10)
                ->withHeaders(['X-API-Key' => $this->baileysKey])
                ->post("{$this->baileysUrl}/send", [
                    'target'  => $phone,
                    'message' => $message,
                ]);

            $data    = $response->json();
            $success = $response->successful() && ($data['success'] ?? false);

            $logEntry?->update([
                'status'   => $success ? 'sent' : 'failed',
                'response' => json_encode($data),
                'sent_at'  => $success ? now() : null,
            ]);

            return $success;

        } catch (\Exception $e) {
            Log::error('Baileys send error: ' . $e->getMessage());
            $logEntry?->update(['status' => 'failed', 'response' => $e->getMessage()]);
            return false;
        }
    }

    private function dispatchBulk(array $targets, string $message, ?int $transactionId = null): array
    {
        $results = ['success' => 0, 'failed' => 0, 'targets' => []];

        try {
            $response = Http::timeout(30)
                ->withHeaders(['X-API-Key' => $this->baileysKey])
                ->post("{$this->baileysUrl}/send-bulk", [
                    'targets' => $targets,
                    'message' => $message,
                ]);

            $data = $response->json();

            if ($response->successful() && isset($data['results'])) {
                foreach ($data['results'] as $r) {
                    $ok = $r['ok'] ?? false;
                    $results[$ok ? 'success' : 'failed']++;
                    $results['targets'][] = ['phone' => $r['target'], 'ok' => $ok];

                    // Log per target
                    $this->log(
                        $r['target'], $message, $transactionId,
                        $ok ? 'sent' : 'failed',
                        $ok ? null : ($r['error'] ?? 'unknown')
                    );
                }
            } else {
                // Fallback: dispatch satu-satu
                foreach ($targets as $t) {
                    $ok = $this->dispatch($t, $message, $transactionId);
                    $results[$ok ? 'success' : 'failed']++;
                    $results['targets'][] = ['phone' => $t, 'ok' => $ok];
                }
            }

        } catch (\Exception $e) {
            Log::error('Baileys bulk error: ' . $e->getMessage());
            // Fallback satu-satu
            foreach ($targets as $t) {
                $ok = $this->dispatch($t, $message, $transactionId);
                $results[$ok ? 'success' : 'failed']++;
                $results['targets'][] = ['phone' => $t, 'ok' => $ok];
            }
        }

        return $results;
    }

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