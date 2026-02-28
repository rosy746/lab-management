<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Resource;
use Carbon\Carbon;

class BookingController extends Controller
{
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

    public function index(Request $request)
    {
        $allowed = $this->getAllowedResources();

        $query = Booking::with(['resource', 'timeSlot'])
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->orderBy('created_at', 'desc');

        if ($allowed !== null) $query->whereIn('resource_id', $allowed);
        if ($request->status && $request->status !== 'all') $query->where('status', $request->status);
        if ($request->resource_id) $query->where('resource_id', $request->resource_id);
        if ($request->date) $query->where('booking_date', $request->date);
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('teacher_name', 'like', '%'.$request->search.'%')
                  ->orWhere('class_name', 'like', '%'.$request->search.'%')
                  ->orWhere('title', 'like', '%'.$request->search.'%');
            });
        }

        $bookings  = $query->paginate(15)->withQueryString();

        $resQuery = Resource::where('status', 'active')->orderBy('name');
        if ($allowed !== null) $resQuery->whereIn('id', $allowed);
        $resources = $resQuery->get();

        $statsQuery = Booking::query();
        if ($allowed !== null) $statsQuery->whereIn('resource_id', $allowed);
        $stats = [
            'pending'  => (clone $statsQuery)->where('status', 'pending')->count(),
            'approved' => (clone $statsQuery)->where('status', 'approved')->count(),
            'rejected' => (clone $statsQuery)->where('status', 'rejected')->count(),
            'total'    => (clone $statsQuery)->count(),
        ];

        return view('booking.index', compact('bookings', 'resources', 'stats'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['resource', 'timeSlot']);
        return view('booking.show', compact('booking'));
    }

    private function checkResourceAccess(Booking $booking): bool
    {
        $allowed = $this->getAllowedResources();
        if ($allowed === null) return true;
        return in_array($booking->resource_id, $allowed);
    }

    public function approveGroup(Request $request)
    {
        $request->validate([
            'teacher_name' => 'required|string',
            'resource_id'  => 'required|integer',
            'booking_date' => 'required|date',
        ]);

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

        foreach ($bookings as $booking) {
            $booking->update([
                'status'      => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'notes'       => $request->notes,
            ]);
        }

        // Generate/extend session untuk semua slot
        $session = null;
        foreach ($bookings as $booking) {
            $session = \App\Http\Controllers\LabControlController::generateFromBooking($booking);
        }

        // Kirim WA sekali setelah semua slot selesai di-extend
        if ($session) {
            (new \App\Http\Controllers\LabControlController)->sendWebhookPublic($session->fresh());
        }

        return back()->with('success', "{$bookings->count()} slot booking berhasil disetujui sekaligus.");
    }

    public function approve(Request $request, Booking $booking)
    {
        if (!$this->checkResourceAccess($booking)) {
            return back()->with('error', 'Anda tidak memiliki akses ke lab ini.');
        }
        if ($booking->status !== 'pending') {
            return back()->with('error', 'Booking ini sudah diproses sebelumnya.');
        }
        $booking->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'notes'       => $request->notes,
        ]);

        // Generate token dan kirim WA
        $session = \App\Http\Controllers\LabControlController::generateFromBooking($booking);
        if ($session) {
            (new \App\Http\Controllers\LabControlController)->sendWebhookPublic($session->fresh());
        }

        return back()->with('success', 'Booking "'.$booking->title.'" berhasil disetujui.');
    }

    public function reject(Request $request, Booking $booking)
    {
        if (!$this->checkResourceAccess($booking)) {
            return back()->with('error', 'Anda tidak memiliki akses ke lab ini.');
        }
        if ($booking->status !== 'pending') {
            return back()->with('error', 'Booking ini sudah diproses sebelumnya.');
        }

        $request->validate(['notes' => 'required|string|min:5'], [
            'notes.required' => 'Alasan penolakan wajib diisi.',
            'notes.min'      => 'Alasan minimal 5 karakter.',
        ]);

        $booking->update([
            'status' => 'rejected',
            'notes'  => $request->notes,
        ]);

        return back()->with('success', 'Booking "'.$booking->title.'" telah ditolak.');
    }

    public function destroy(Booking $booking)
    {
        if (!$this->checkResourceAccess($booking)) {
            return back()->with('error', 'Anda tidak memiliki akses ke lab ini.');
        }
        $title = $booking->title;
        $booking->delete();
        return back()->with('success', 'Booking "'.$title.'" berhasil dihapus.');
    }
}