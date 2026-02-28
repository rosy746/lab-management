<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\WaSetting;
use App\Models\Finance\WaNotificationLog;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class WaSettingController extends Controller
{
    public function index()
    {
        $setting = WaSetting::first() ?? new WaSetting();
        $logs    = WaNotificationLog::orderByDesc('created_at')->limit(20)->get();

        return view('finance.wa-settings.index', compact('setting', 'logs'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'fonnte_token'            => 'required|string',
            'device_number'           => 'nullable|string',
            'notify_on_transaction'   => 'boolean',
            'notify_on_budget_exceeded' => 'boolean',
            'budget_alert_threshold'  => 'required|integer|min:1|max:100',
            'is_active'               => 'boolean',
            // Target phones dikirim sebagai array dari form
            'target_phones'           => 'nullable|array',
            'target_phones.*'         => 'nullable|string',
        ]);

        // Bersihkan array — buang entri kosong, trim spasi
        $targets = collect($request->input('target_phones', []))
            ->map(fn($t) => trim($t))
            ->filter(fn($t) => $t !== '')
            ->values()
            ->toArray();

        // Primary target = target pertama (untuk kompatibilitas legacy)
        $primaryPhone = $targets[0] ?? null;

        WaSetting::updateOrCreate(['id' => 1], [
            'fonnte_token'               => $request->fonnte_token,
            'device_number'              => $request->device_number,
            'admin_phone'                => $primaryPhone,
            'target_phones'              => $targets,
            'notify_income'              => $request->boolean('notify_on_transaction'),
            'notify_expense'             => $request->boolean('notify_on_transaction'),
            'notify_on_transaction'      => $request->boolean('notify_on_transaction'),
            'notify_budget_warning'      => $request->boolean('notify_on_budget_exceeded'),
            'notify_on_budget_exceeded'  => $request->boolean('notify_on_budget_exceeded'),
            'budget_warning_pct'         => $request->budget_alert_threshold,
            'budget_alert_threshold'     => $request->budget_alert_threshold,
            'is_active'                  => true,
        ]);

        return back()->with('success', 'Pengaturan WhatsApp berhasil disimpan. (' . count($targets) . ' target aktif)');
    }

    public function test(Request $request, WhatsAppService $wa)
    {
        $setting = WaSetting::first();

        if (!$setting || !$setting->fonnte_token) {
            return response()->json(['success' => false, 'message' => 'Token Fonnte belum dikonfigurasi.']);
        }

        $targets = $setting->getAllTargets();

        if (empty($targets)) {
            return response()->json(['success' => false, 'message' => 'Belum ada nomor target yang ditambahkan.']);
        }

        $message = implode("\n", [
            '🧪 *TEST NOTIFIKASI*',
            '━━━━━━━━━━━━━━━━━━━━',
            '✅ Konfigurasi WhatsApp berhasil!',
            '📅 Waktu: ' . now()->format('d/m/Y H:i:s'),
            '🎯 Target: ' . count($targets) . ' nomor/grup',
            '━━━━━━━━━━━━━━━━━━━━',
            'Sistem keuangan tim siap mengirim notifikasi.',
        ]);

        $results = $wa->sendToAll($message);

        $allOk = $results['failed'] === 0 && $results['success'] > 0;

        return response()->json([
            'success'  => $allOk,
            'message'  => "Terkirim: {$results['success']} | Gagal: {$results['failed']} dari " . count($targets) . ' target',
            'results'  => $results['targets'],
        ]);
    }
}