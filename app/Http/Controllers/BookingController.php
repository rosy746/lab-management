<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Booking;
use App\Models\Resource;
use App\Models\SundayBooking;
use Carbon\Carbon;

class BookingController extends Controller
{
    // ══════════════════════════════════════════════════════════════════
    // ACCESS CONTROL HELPERS
    // ══════════════════════════════════════════════════════════════════

    /**
     * Null = admin/operator (akses semua)
     * Array = daftar resource_id yang boleh diakses
     */
    private function getAllowedResources(): ?array
    {
        $user = auth()->user();
        if ($user->role === 'admin' || $user->role === 'operator') {
            return null;
        }
        $meta = is_array($user->metadata)
            ? $user->metadata
            : json_decode($user->metadata, true);
        return $meta['allowed_resources'] ?? [];
    }

    private function checkResourceAccess(int $resourceId): bool
    {
        $allowed = $this->getAllowedResources();
        if ($allowed === null) return true;
        return in_array($resourceId, $allowed);
    }

    // ══════════════════════════════════════════════════════════════════
    // INDEX
    // ══════════════════════════════════════════════════════════════════

    public function index(Request $request)
    {
        $allowed = $this->getAllowedResources();

        // ── Regular bookings ──
        $query = Booking::with(['resource', 'timeSlot'])
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->orderBy('created_at', 'desc');

        if ($allowed !== null)                              $query->whereIn('resource_id', $allowed);
        if ($request->status && $request->status !== 'all') $query->where('status', $request->status);
        if ($request->resource_id)                          $query->where('resource_id', $request->resource_id);
        if ($request->date)                                 $query->where('booking_date', $request->date);
        if ($request->search) {
            $query->where(fn($q) => $q
                ->where('teacher_name', 'like', '%' . $request->search . '%')
                ->orWhere('class_name',  'like', '%' . $request->search . '%')
                ->orWhere('title',       'like', '%' . $request->search . '%')
            );
        }

        $bookings = $query->paginate(15, ['*'], 'bookings_page')->withQueryString();

        // ── Sunday bookings ──
        $sunQuery = SundayBooking::with(['resource', 'organization'])
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->orderBy('created_at', 'desc');

        if ($allowed !== null)                              $sunQuery->whereIn('resource_id', $allowed);
        if ($request->status && $request->status !== 'all') $sunQuery->where('status', $request->status);
        if ($request->resource_id)                          $sunQuery->where('resource_id', $request->resource_id);
        if ($request->date)                                 $sunQuery->where('booking_date', $request->date);
        if ($request->search) {
            $sunQuery->where(fn($q) => $q
                ->where('teacher_name', 'like', '%' . $request->search . '%')
                ->orWhere('class_name',  'like', '%' . $request->search . '%')
                ->orWhere('title',       'like', '%' . $request->search . '%')
            );
        }

        $sundayBookings = $sunQuery->paginate(10, ['*'], 'sunday_page')->withQueryString();

        // ── Resources ──
        $resQuery = Resource::where('status', 'active')->orderBy('name');
        if ($allowed !== null) $resQuery->whereIn('id', $allowed);
        $resources = $resQuery->get();

        // ── FIX: Stats — 1 query bukan 4 ──
        $statsQuery = Booking::query();
        if ($allowed !== null) $statsQuery->whereIn('resource_id', $allowed);

        $statsRaw = (clone $statsQuery)
            ->selectRaw("
                COUNT(*) as total,
                SUM(status = 'pending')  as pending,
                SUM(status = 'approved') as approved,
                SUM(status = 'rejected') as rejected
            ")
            ->first();

        $stats = [
            'total'    => (int) ($statsRaw->total    ?? 0),
            'pending'  => (int) ($statsRaw->pending  ?? 0),
            'approved' => (int) ($statsRaw->approved ?? 0),
            'rejected' => (int) ($statsRaw->rejected ?? 0),
        ];

        return view('booking.index', compact('bookings', 'sundayBookings', 'resources', 'stats'));
    }

    // ══════════════════════════════════════════════════════════════════
    // SHOW
    // ══════════════════════════════════════════════════════════════════

    public function show(Booking $booking)
    {
        if (!$this->checkResourceAccess($booking->resource_id)) {
            return back()->with('error', 'Anda tidak memiliki akses ke lab ini.');
        }
        $booking->load(['resource', 'timeSlot']);
        return view('booking.show', compact('booking'));
    }

    // ══════════════════════════════════════════════════════════════════
    // APPROVE GROUP
    // ══════════════════════════════════════════════════════════════════

    public function approveGroup(Request $request)
    {
        $request->validate([
            'teacher_name' => 'required|string',
            'resource_id'  => 'required|integer|exists:resources,id',
            'booking_date' => 'required|date',
        ]);

        if (!$this->checkResourceAccess((int) $request->resource_id)) {
            return back()->with('error', 'Anda tidak memiliki akses ke lab ini.');
        }

        $allowed = $this->getAllowedResources();

        $bookings = Booking::where('teacher_name', $request->teacher_name)
            ->where('resource_id', $request->resource_id)
            ->where('booking_date', $request->booking_date)
            ->where('status', 'pending')
            ->when($allowed !== null, fn($q) => $q->whereIn('resource_id', $allowed))
            ->orderBy('time_slot_id')
            ->get();

        if ($bookings->isEmpty()) {
            return back()->with('error', 'Tidak ada booking pending yang bisa disetujui.');
        }

        try {
            DB::transaction(function () use ($bookings, $request) {
                // ── FIX: bulk update — 1 query bukan N ──
                Booking::whereIn('id', $bookings->pluck('id'))
                    ->lockForUpdate()
                    ->update([
                        'status'      => 'approved',
                        'approved_by' => auth()->id(),
                        'approved_at' => now(),
                        'notes'       => $request->notes,
                    ]);
            });

        } catch (\Exception $e) {
            Log::error('approveGroup failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyetujui booking. Silakan coba lagi.');
        }

        // Generate session di luar transaction — error tidak rollback approve
        $session = null;
        foreach ($bookings as $booking) {
            try {
                $session = LabControlController::generateFromBooking($booking->fresh());
            } catch (\Exception $e) {
                Log::warning('generateFromBooking failed for booking #' . $booking->id . ': ' . $e->getMessage());
            }
        }

        if ($session) {
            try {
                (new LabControlController)->sendWebhookPublic($session->fresh());
            } catch (\Exception $e) {
                Log::warning('sendWebhookPublic failed: ' . $e->getMessage());
            }
        }

        return back()->with('success', "{$bookings->count()} slot booking berhasil disetujui sekaligus.");
    }

    // ══════════════════════════════════════════════════════════════════
    // APPROVE
    // ══════════════════════════════════════════════════════════════════

    public function approve(Request $request, Booking $booking)
    {
        if (!$this->checkResourceAccess($booking->resource_id)) {
            return back()->with('error', 'Anda tidak memiliki akses ke lab ini.');
        }
        if ($booking->status !== 'pending') {
            return back()->with('error', 'Booking ini sudah diproses sebelumnya.');
        }

        try {
            DB::transaction(function () use ($booking, $request) {
                $booking->lockForUpdate();
                $booking->update([
                    'status'      => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'notes'       => $request->notes,
                ]);
            });

        } catch (\Exception $e) {
            Log::error('approve failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyetujui booking. Silakan coba lagi.');
        }

        // Generate session di luar transaction
        try {
            $session = LabControlController::generateFromBooking($booking->fresh());
            if ($session) {
                (new LabControlController)->sendWebhookPublic($session->fresh());
            }
        } catch (\Exception $e) {
            Log::warning('Session generation failed for booking #' . $booking->id . ': ' . $e->getMessage());
        }

        return back()->with('success', 'Booking "' . $booking->title . '" berhasil disetujui.');
    }

    // ══════════════════════════════════════════════════════════════════
    // REJECT
    // ══════════════════════════════════════════════════════════════════

    public function reject(Request $request, $id)
    {
        $type = $request->input('type', 'regular');

        // ── FIX: pakai findOrFail dengan model yang tepat ──
        $booking = $type === 'sunday'
            ? SundayBooking::findOrFail($id)
            : Booking::findOrFail($id);

        // ── FIX: access control untuk reject juga ──
        if (!$this->checkResourceAccess($booking->resource_id)) {
            return back()->with('error', 'Anda tidak memiliki akses ke lab ini.');
        }

        if ($booking->status !== 'pending') {
            return back()->with('error', 'Booking ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'notes' => 'required|string|min:5|max:500',
        ], [
            'notes.required' => 'Alasan penolakan wajib diisi.',
            'notes.min'      => 'Alasan minimal 5 karakter.',
        ]);

        $booking->update([
            'status' => 'rejected',
            'notes'  => $request->notes,
        ]);

        return back()->with('success', 'Booking "' . $booking->title . '" telah ditolak.');
    }

    // ══════════════════════════════════════════════════════════════════
    // DESTROY
    // ══════════════════════════════════════════════════════════════════

    public function destroy(Booking $booking)
    {
        if (!$this->checkResourceAccess($booking->resource_id)) {
            return back()->with('error', 'Anda tidak memiliki akses ke lab ini.');
        }
        $title = $booking->title;
        $booking->delete();
        return back()->with('success', 'Booking "' . $title . '" berhasil dihapus.');
    }

    // ── FIX: tambah access control di destroySunday ──
    public function destroySunday($id)
    {
        $booking = SundayBooking::findOrFail($id);

        if (!$this->checkResourceAccess($booking->resource_id)) {
            return back()->with('error', 'Anda tidak memiliki akses ke lab ini.');
        }

        $title = $booking->title;
        $booking->delete();
        return back()->with('success', 'Booking Minggu "' . $title . '" berhasil dihapus.');
    }

    // ══════════════════════════════════════════════════════════════════
    // APPROVE SUNDAY
    // ══════════════════════════════════════════════════════════════════

    public function approveSunday(Request $request, $id)
    {
        $booking = SundayBooking::findOrFail($id);

        if (!$this->checkResourceAccess($booking->resource_id)) {
            return back()->with('error', 'Anda tidak memiliki akses ke lab ini.');
        }

        if ($booking->status !== 'pending') {
            return back()->with('error', 'Booking ini sudah diproses sebelumnya.');
        }

        try {
            DB::transaction(function () use ($booking, $request) {
                $booking->lockForUpdate();
                $booking->update([
                    'status'      => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'notes'       => $request->notes,
                ]);
            });

        } catch (\Exception $e) {
            Log::error('approveSunday failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyetujui booking. Silakan coba lagi.');
        }

        return back()->with('success', 'Booking Minggu "' . $booking->title . '" berhasil disetujui.');
    }
}