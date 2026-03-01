<?php
// app/Http/Controllers/BotController.php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Resource;
use App\Models\TimeSlot;
use App\Models\Teacher;
use App\Models\LabSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BotController extends Controller
{
    // ================================================================
    // JADWAL
    // ================================================================

    /**
     * GET /api/bot/jadwal/hari-ini
     * Jadwal hari ini berdasarkan hari dalam seminggu
     */
    public function jadwalHariIni()
    {
        $today  = Carbon::now();
        $dayEn  = $today->format('l'); // 'Monday', 'Tuesday', dst

        $schedules = Schedule::with(['timeSlot', 'resource', 'class'])
            ->where('day_of_week', $dayEn)
            ->where('status', 'active')
            ->orderBy('time_slot_id')
            ->get()
            ->map(fn($s) => [
                'id'           => $s->id,
                'lab_name'     => $s->resource->name ?? '-',
                'teacher_name' => $s->teacher_name,
                'subject'      => $s->subject_name,
                'class_name'   => $s->class?->name ?? $s->class_name ?? '-',
                'start_time'   => substr($s->timeSlot->start_time ?? '', 0, 5),
                'end_time'     => substr($s->timeSlot->end_time ?? '', 0, 5),
                'day'          => $dayEn,
            ]);

        return response()->json([
            'success'  => true,
            'date'     => $today->format('d/m/Y'),
            'day'      => $dayEn,
            'data'     => $schedules,
        ]);
    }

    /**
     * GET /api/bot/jadwal/minggu-ini
     * Semua jadwal aktif minggu ini
     */
    public function jadwalMingguIni()
    {
        $dayOrder = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

        $schedules = Schedule::with(['timeSlot', 'resource', 'class'])
            ->where('status', 'active')
            ->whereIn('day_of_week', $dayOrder)
            ->get()
            ->map(fn($s) => [
                'id'           => $s->id,
                'day'          => $s->day_of_week,
                'lab_name'     => $s->resource->name ?? '-',
                'teacher_name' => $s->teacher_name,
                'subject'      => $s->subject_name,
                'class_name'   => $s->class?->name ?? $s->class_name ?? '-',
                'start_time'   => substr($s->timeSlot->start_time ?? '', 0, 5),
                'end_time'     => substr($s->timeSlot->end_time ?? '', 0, 5),
                'slot_order'   => $s->timeSlot->slot_order ?? 0,
            ])
            ->sortBy(fn($s) => [array_search($s['day'], $dayOrder), $s['slot_order']])
            ->values();

        return response()->json([
            'success' => true,
            'data'    => $schedules,
        ]);
    }

    // ================================================================
    // BOOKING
    // ================================================================

    /**
     * GET /api/bot/booking/pending
     * Semua booking yang menunggu persetujuan
     */
    public function bookingPending()
    {
        $bookings = Booking::with(['resource', 'timeSlot'])
            ->where('status', 'pending')
            ->orderBy('booking_date')
            ->get()
            ->map(fn($b) => [
                'id'           => $b->id,
                'lab_name'     => $b->resource->name ?? '-',
                'teacher_name' => $b->teacher_name,
                'teacher_phone'=> $b->teacher_phone,
                'booking_date' => $b->booking_date->format('d/m/Y'),
                'class_name'   => $b->class_name,
                'subject_name' => $b->subject_name,
                'slot_name'    => $b->timeSlot->name ?? '-',
                'start_time'   => substr($b->timeSlot->start_time ?? '', 0, 5),
                'end_time'     => substr($b->timeSlot->end_time ?? '', 0, 5),
            ]);

        return response()->json([
            'success' => true,
            'data'    => $bookings,
            'total'   => $bookings->count(),
        ]);
    }

    /**
     * GET /api/bot/booking/hari-ini
     * Semua booking hari ini (semua status)
     */
    public function bookingHariIni()
    {
        $today = Carbon::today();

        $bookings = Booking::with(['resource', 'timeSlot'])
            ->whereDate('booking_date', $today)
            ->orderBy('time_slot_id')
            ->get()
            ->map(fn($b) => [
                'id'           => $b->id,
                'status'       => $b->status,
                'lab_name'     => $b->resource->name ?? '-',
                'teacher_name' => $b->teacher_name,
                'booking_date' => $b->booking_date->format('d/m/Y'),
                'class_name'   => $b->class_name,
                'subject_name' => $b->subject_name,
                'start_time'   => substr($b->timeSlot->start_time ?? '', 0, 5),
                'end_time'     => substr($b->timeSlot->end_time ?? '', 0, 5),
            ]);

        return response()->json([
            'success' => true,
            'date'    => $today->format('d/m/Y'),
            'data'    => $bookings,
        ]);
    }

    /**
     * POST /api/bot/booking/create
     * Buat booking baru dari bot
     * Body: { phone, lab_id, tanggal, slot_id, class_name, subject }
     */
    public function bookingCreate(Request $request)
    {
        $request->validate([
            'phone'      => 'required|string',
            'lab_id'     => 'required|integer',
            'tanggal'    => 'required|date_format:d/m/Y',
            'slot_id'    => 'required|integer',
            'class_name' => 'required|string|max:100',
            'subject'    => 'required|string|max:100',
        ]);

        // Kenali guru dari nomor HP
        $teacher = Teacher::whereRaw(
            "REPLACE(REPLACE(phone, '+', ''), ' ', '') = ?",
            [$request->phone]
        )->first();

        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Guru tidak ditemukan untuk nomor: ' . $request->phone,
            ], 404);
        }

        $tanggal = Carbon::createFromFormat('d/m/Y', $request->tanggal)->toDateString();

        // Cek slot sudah terpakai atau belum
        $conflict = Booking::where('resource_id', $request->lab_id)
            ->where('time_slot_id', $request->slot_id)
            ->whereDate('booking_date', $tanggal)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($conflict) {
            return response()->json([
                'success' => false,
                'message' => 'Slot sudah terpakai untuk lab dan tanggal tersebut.',
            ], 409);
        }

        $lab  = Resource::find($request->lab_id);
        $slot = TimeSlot::find($request->slot_id);

        if (!$lab || !$slot) {
            return response()->json([
                'success' => false,
                'message' => 'Lab atau slot tidak ditemukan.',
            ], 404);
        }

        $booking = Booking::create([
            'resource_id'      => $request->lab_id,
            'time_slot_id'     => $request->slot_id,
            'organization_id'  => $teacher->organization_id ?? 1,
            'booking_date'     => $tanggal,
            'teacher_name'     => $teacher->name,
            'teacher_phone'    => $teacher->phone,
            'class_name'       => $request->class_name,
            'subject_name'     => $request->subject,
            'title'            => $request->subject . ' - ' . $request->class_name,
            'participant_count'=> 0,
            'status'           => 'pending',
        ]);

        return response()->json([
            'success'      => true,
            'booking_id'   => $booking->id,
            'lab_name'     => $lab->name,
            'slot_name'    => $slot->name,
            'start_time'   => substr($slot->start_time, 0, 5),
            'end_time'     => substr($slot->end_time, 0, 5),
            'teacher_name' => $teacher->name,
            'tanggal'      => $request->tanggal,
            'class_name'   => $booking->class_name,
            'subject'      => $booking->subject_name,
        ]);
    }

    /**
     * POST /api/bot/booking/{id}/approve
     * Approve booking + generate lab session
     */
    public function bookingApprove(Request $request, int $id)
    {
        $booking = Booking::with(['resource', 'timeSlot'])->find($id);

        if (!$booking) {
            return response()->json(['success' => false, 'message' => "Booking #$id tidak ditemukan."], 404);
        }
        if ($booking->status !== 'pending') {
            return response()->json(['success' => false, 'message' => "Booking #$id sudah berstatus {$booking->status}."], 409);
        }

        // Ambil admin dari phone yang approve
        $approver = User::whereRaw(
            "REPLACE(REPLACE(phone, '+', ''), ' ', '') = ?",
            [$request->approver_phone ?? '']
        )->first();

        $booking->update([
            'status'      => 'approved',
            'approved_by' => $approver?->id,
            'approved_at' => now(),
        ]);

        // Generate lab session
        $session = \App\Http\Controllers\LabControlController::generateFromBooking($booking);

        // Kirim WA ke guru jika session berhasil dibuat
        if ($session) {
            (new LabControlController)->sendWebhookPublic($session);
        }

        return response()->json([
            'success'      => true,
            'booking_id'   => $booking->id,
            'teacher_name' => $booking->teacher_name,
            'lab_name'     => $booking->resource->name ?? '-',
            'session_token'=> $session?->token,
        ]);
    }

    /**
     * POST /api/bot/booking/{id}/reject
     * Tolak booking
     * Body: { approver_phone, alasan }
     */
    public function bookingReject(Request $request, int $id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json(['success' => false, 'message' => "Booking #$id tidak ditemukan."], 404);
        }
        if ($booking->status !== 'pending') {
            return response()->json(['success' => false, 'message' => "Booking #$id sudah berstatus {$booking->status}."], 409);
        }

        $alasan = $request->alasan ?? 'Ditolak oleh admin';

        $approver = User::whereRaw(
            "REPLACE(REPLACE(phone, '+', ''), ' ', '') = ?",
            [$request->approver_phone ?? '']
        )->first();

        $booking->update([
            'status'      => 'rejected',
            'notes'       => $alasan,
            'approved_by' => $approver?->id,
            'approved_at' => now(),
        ]);

        return response()->json([
            'success'       => true,
            'booking_id'    => $booking->id,
            'teacher_name'  => $booking->teacher_name,
            'teacher_phone' => $booking->teacher_phone,
            'alasan'        => $alasan,
        ]);
    }

    // ================================================================
    // LAB & RESOURCES
    // ================================================================

    /**
     * GET /api/bot/labs
     * Daftar semua lab aktif
     */
    public function labs()
    {
        $labs = Resource::where('is_active', 1)
            ->whereIn('type', ['lab', 'computer_lab'])
            ->orderBy('id')
            ->get(['id', 'name', 'type', 'status']);

        return response()->json([
            'success' => true,
            'data'    => $labs,
        ]);
    }

    /**
     * GET /api/bot/labs/{id}/slots?tanggal=DD/MM/YYYY
     * Slot yang tersedia untuk lab tertentu pada tanggal tertentu
     */
    public function labSlots(Request $request, int $labId)
    {
        $request->validate(['tanggal' => 'required|date_format:d/m/Y']);

        $tanggal = Carbon::createFromFormat('d/m/Y', $request->tanggal)->toDateString();

        // Slot yang sudah dipesan
        $bookedIds = Booking::where('resource_id', $labId)
            ->whereDate('booking_date', $tanggal)
            ->whereIn('status', ['pending', 'approved'])
            ->pluck('time_slot_id')
            ->toArray();

        $slots = TimeSlot::where('is_active', 1)
            ->where('is_break', 0)
            ->orderBy('slot_order')
            ->get()
            ->map(fn($s) => [
                'id'         => $s->id,
                'name'       => $s->name,
                'start_time' => substr($s->start_time, 0, 5),
                'end_time'   => substr($s->end_time, 0, 5),
                'available'  => !in_array($s->id, $bookedIds),
            ]);

        return response()->json([
            'success' => true,
            'lab_id'  => $labId,
            'tanggal' => $request->tanggal,
            'data'    => $slots,
        ]);
    }

    // ================================================================
    // SESI LAB
    // ================================================================

    /**
     * GET /api/bot/sesi/aktif
     * Semua sesi lab yang sedang aktif
     */
    public function sesiAktif()
    {
        $now = now();

        $sessions = LabSession::with('resource')
            ->where('is_active', true)
            ->whereNull('invalidated_at')
            ->where('session_end', '>', $now)
            ->orderBy('session_start')
            ->get()
            ->map(fn($s) => [
                'token'        => $s->token,
                'lab_name'     => $s->resource->name ?? $s->lab_key,
                'teacher_name' => $s->teacher_name,
                'session_start'=> $s->session_start->format('H:i'),
                'session_end'  => $s->session_end->format('H:i'),
                'sisa_menit'   => (int) $s->session_end->diffInMinutes($now),
                'checked_in'   => !is_null($s->checked_in_at),
            ]);

        return response()->json([
            'success' => true,
            'data'    => $sessions,
            'total'   => $sessions->count(),
        ]);
    }

    // ================================================================
    // USER IDENTITY
    // ================================================================

    /**
     * POST /api/bot/identify
     * Kenali user dari nomor HP
     * Body: { phone }
     */
    public function identify(Request $request)
    {
        $request->validate(['phone' => 'required|string']);

        $phone = $this->normalizePhone($request->phone);

        // Cek tabel users (admin/operator/teknisi)
        $user = User::whereRaw(
            "REPLACE(REPLACE(phone, '+', ''), ' ', '') = ?", [$phone]
        )->where('is_active', 1)->whereNull('deleted_at')->first();

        if ($user) {
            return response()->json([
                'success' => true,
                'found'   => true,
                'id'      => $user->id,
                'name'    => $user->full_name,
                'role'    => $user->role,
                'phone'   => $phone,
                'source'  => 'users',
                'metadata'=> $user->metadata,
            ]);
        }

        // Cek tabel teachers (guru)
        $teacher = Teacher::whereRaw(
            "REPLACE(REPLACE(phone, '+', ''), ' ', '') = ?", [$phone]
        )->where('is_active', 1)->first();

        if ($teacher) {
            return response()->json([
                'success' => true,
                'found'   => true,
                'id'      => $teacher->id,
                'name'    => $teacher->name,
                'role'    => 'guru',
                'phone'   => $phone,
                'source'  => 'teachers',
                'metadata'=> null,
            ]);
        }

        return response()->json([
            'success' => true,
            'found'   => false,
        ]);
    }

    // ================================================================
    // HELPERS
    // ================================================================

    private function normalizePhone(string $phone): string
    {
        $phone = ltrim(trim($phone), '+');
        $phone = explode('@', $phone)[0];
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (str_starts_with($phone, '8')) {
            $phone = '62' . $phone;
        }
        return $phone;
    }
}