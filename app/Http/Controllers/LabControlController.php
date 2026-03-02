<?php
namespace App\Http\Controllers;

use App\Models\LabSession;
use App\Services\MikroTikService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LabControlController extends Controller
{
    public function control(Request $request, string $token)
    {
        $token = strtoupper(trim($token));

        $session = LabSession::where('token', $token)
            ->where('is_active', true)
            ->whereNull('invalidated_at')
            ->where('session_end', '>', now())
            ->where('session_start', '<=', now()->addMinutes(5))
            ->first();

        if (!$session) {
            return view('lab-control.invalid', [
                'message' => 'Link tidak valid, sudah expired, atau sesi belum dimulai.'
            ]);
        }

        $session->markAsUsed();
        $labName = LabSession::LAB_MAP[$session->lab_key]['name'] ?? $session->lab_key;

        return view('lab-control.control', compact('session', 'labName', 'token'));
    }

    public function status(Request $request, string $token)
    {
        $session = $this->findSession($token);
        if (!$session) {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);
        }

        try {
            $mikrotik = new MikroTikService();
            $status = $mikrotik->getLabStatus($session->lab_key);
            return response()->json($status);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function toggleInternet(Request $request, string $token)
    {
        $session = $this->findSession($token);
        if (!$session) {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);
        }

        $request->validate(['action' => 'required|in:on,off']);

        try {
            $mikrotik = new MikroTikService();
            $result = $request->action === 'on'
                ? $mikrotik->enableLab($session->lab_key)
                : $mikrotik->disableLab($session->lab_key);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function logout(Request $request, string $token)
    {
        $session = $this->findSession($token);
        if ($session) {
            $session->invalidate('logout');
        }

        return view('lab-control.invalid', [
            'message' => 'Sesi telah diakhiri. Link ini tidak dapat digunakan lagi.'
        ]);
    }

    public function generateToken(Request $request)
    {
        $request->validate([
            'lab_key'       => 'required|in:lab7,lab8',
            'teacher_name'  => 'required|string|max:100',
            'teacher_phone' => 'nullable|string|max:20',
            'duration'      => 'required|integer|min:30|max:480',
        ]);

        $session = LabSession::create([
            'token'         => LabSession::generateToken(),
            'lab_key'       => $request->lab_key,
            'resource_id'   => LabSession::LAB_MAP[$request->lab_key]['resource_id'],
            'source_type'   => 'manual',
            'teacher_name'  => $request->teacher_name,
            'teacher_phone' => $request->teacher_phone,
            'session_start' => now(),
            'session_end'   => now()->addMinutes($request->duration),
        ]);

        $this->sendWebhook($session);

        return back()->with('success', "Token {$session->token} berhasil dibuat · Link: " . route('lab.control', $session->token));
    }

    // ============================================================
    // STATIC: Generate dari booking (tanpa kirim WA — caller yang kirim)
    // ============================================================
    public static function generateFromBooking($booking): ?LabSession
    {
        $labKey = null;
        foreach (LabSession::LAB_MAP as $key => $config) {
            if ($config['resource_id'] == $booking->resource_id) {
                $labKey = $key;
                break;
            }
        }
        if (!$labKey) return null;

        $start = $booking->booking_date->setTimeFrom(
            \Carbon\Carbon::parse($booking->timeSlot->start_time ?? '07:00')
        );
        $end = $booking->booking_date->setTimeFrom(
            \Carbon\Carbon::parse($booking->timeSlot->end_time ?? '09:00')
        );

        $teacher = \App\Models\Teacher::whereRaw('LOWER(name) = ?', [strtolower($booking->teacher_name)])->first();

        $existingSession = LabSession::where('source_type', 'booking')
            ->where('resource_id', $booking->resource_id)
            ->whereDate('session_start', $booking->booking_date)
            ->where('teacher_name', $booking->teacher_name)
            ->where('is_active', true)
            ->first();

        if ($existingSession) {
            if ($end > $existingSession->session_end) {
                $existingSession->update(['session_end' => $end]);
            }
            return $existingSession;
        }

        $session = LabSession::create([
            'token'         => LabSession::generateToken(),
            'lab_key'       => $labKey,
            'resource_id'   => $booking->resource_id,
            'source_type'   => 'booking',
            'source_id'     => $booking->id,
            'teacher_name'  => $booking->teacher_name,
            'teacher_phone' => $teacher->phone ?? null,
            'session_start' => $start,
            'session_end'   => $end,
        ]);

        // Tidak kirim WA di sini — caller yang kirim setelah semua slot selesai
        return $session;
    }

    // ============================================================
    // STATIC: Generate dari jadwal rutin
    // Cek existing session by teacher+resource+tanggal agar tidak kirim WA 2x
    // ============================================================
    public static function generateFromSchedule($schedule): ?LabSession
    {
        $labKey = null;
        foreach (LabSession::LAB_MAP as $key => $config) {
            if ($config['resource_id'] == $schedule->resource_id) {
                $labKey = $key;
                break;
            }
        }
        if (!$labKey) return null;

        $today = now()->toDateString();
        $start = \Carbon\Carbon::parse("$today {$schedule->start_time}");
        $end   = \Carbon\Carbon::parse("$today {$schedule->end_time}");

        $teacher = \App\Models\Teacher::where('name', $schedule->teacher_name)->first();

        // Cek existing session by source_id (slot yang sama hari ini)
        $existsBySource = LabSession::where('source_type', 'schedule')
            ->where('source_id', $schedule->id)
            ->whereDate('session_start', $today)
            ->exists();
        if ($existsBySource) return null;

        // Cek existing session guru+lab+hari yang sama (jadwal berurutan)
        $existingSession = LabSession::where('source_type', 'schedule')
            ->where('resource_id', $schedule->resource_id)
            ->whereDate('session_start', $today)
            ->where('teacher_name', $schedule->teacher_name)
            ->where('is_active', true)
            ->first();

        if ($existingSession) {
            // Perpanjang session_end saja, tidak kirim WA lagi
            if ($end > $existingSession->session_end) {
                $existingSession->update(['session_end' => $end]);
            }
            return $existingSession;
        }

        $session = LabSession::create([
            'token'         => LabSession::generateToken(),
            'lab_key'       => $labKey,
            'resource_id'   => $schedule->resource_id,
            'source_type'   => 'schedule',
            'source_id'     => $schedule->id,
            'teacher_name'  => $schedule->teacher_name ?? 'Guru',
            'teacher_phone' => $teacher->phone ?? null,
            'session_start' => $start,
            'session_end'   => $end,
        ]);
        return $session;
    }

    // Public wrapper untuk dipakai dari controller lain
    public function sendWebhookPublic(LabSession $session): void
    {
        $this->sendWebhook($session);
    }

    // Kirim webhook ke bot Python
    private function sendWebhook(LabSession $session): void
    {
        $webhookUrl = config('mikrotik.webhook');
        if (!$webhookUrl) return;

        try {
            Http::timeout(10)->withOptions(['verify' => false])->post($webhookUrl, [
                'event'         => 'lab_session_created',
                'token'         => $session->token,
                'lab_name'      => $session->lab_name,
                'teacher_name'  => $session->teacher_name,
                'teacher_phone' => $session->teacher_phone,
                'session_start' => $session->session_start->format('d/m/Y H:i'),
                'session_end'   => $session->session_end->format('d/m/Y H:i'),
                'link'          => route('lab.control', $session->token),
            ]);
        } catch (\Exception $e) {
            // Webhook gagal tidak menghentikan proses
        }
    }

    private function findSession(string $token): ?LabSession
    {
        return LabSession::where('token', strtoupper(trim($token)))
            ->where('is_active', true)
            ->whereNull('invalidated_at')
            ->where('session_end', '>', now())
            ->first();
    }
}