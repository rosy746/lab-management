<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Resource;
use App\Models\Schedule;
use App\Models\TimeSlot;
use App\Models\Booking;
use App\Models\SundayBooking;
use App\Models\Organization;
use App\Models\LabClass;
use App\Models\Teacher;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    private array $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

    private array $dayMapReverse = [
        'Senin'  => 'Monday',
        'Selasa' => 'Tuesday',
        'Rabu'   => 'Wednesday',
        'Kamis'  => 'Thursday',
        'Jumat'  => 'Friday',
        'Sabtu'  => 'Saturday',
        'Minggu' => 'Sunday',
    ];

    // ══════════════════════════════════════════════════════════════════
    // INDEX
    // ══════════════════════════════════════════════════════════════════

    public function index(Request $request)
    {
        $weekStart = $request->get('week')
            ? Carbon::parse($request->get('week'))->startOfWeek(Carbon::MONDAY)
            : Carbon::now()->startOfWeek(Carbon::MONDAY);

        $weekEnd = $weekStart->copy()->addDays(6);

        $resources     = Resource::where('status', 'active')->orderBy('name')->get();
        $timeSlots     = TimeSlot::where('is_active', 1)->orderBy('slot_order')->get();
        $organizations = Organization::where('is_active', 1)->orderBy('name')->get();
        $teachers      = Teacher::where('is_active', true)->orderBy('name')->get(['id', 'name', 'phone']);

        $resourceIds = $resources->pluck('id');

        $schedules = Schedule::with(['labClass'])
            ->where('status', 'active')
            ->whereIn('resource_id', $resourceIds)
            ->get()
            ->groupBy(fn($s) => $s->resource_id . '_' . $s->day_of_week . '_' . $s->time_slot_id);

        $bookings = Booking::whereBetween('booking_date', [$weekStart, $weekEnd])
            ->whereIn('resource_id', $resourceIds)
            ->whereIn('status', ['pending', 'approved'])
            ->get()
            ->groupBy(fn($b) => $b->resource_id . '_' . Carbon::parse($b->booking_date)->toDateString() . '_' . $b->time_slot_id);

        $sundayBookings = SundayBooking::whereBetween('booking_date', [$weekStart, $weekEnd])
            ->whereIn('resource_id', $resourceIds)
            ->whereIn('status', ['pending', 'approved'])
            ->get()
            ->groupBy(fn($b) => $b->resource_id . '_' . Carbon::parse($b->booking_date)->toDateString());

        $weekDates = [];
        foreach ($this->days as $i => $day) {
            $weekDates[$day] = $weekStart->copy()->addDays($i)->toDateString();
        }

        $prevWeek = $weekStart->copy()->subWeek()->toDateString();
        $nextWeek = $weekStart->copy()->addWeek()->toDateString();

        return view('schedule.index', compact(
            'resources', 'timeSlots', 'schedules', 'bookings', 'sundayBookings',
            'weekDates', 'weekStart', 'weekEnd', 'organizations',
            'prevWeek', 'nextWeek', 'teachers'
        ))->with('days', $this->days)->with('dayMapReverse', $this->dayMapReverse);
    }

    // ══════════════════════════════════════════════════════════════════
    // GET CLASSES (AJAX)
    // ══════════════════════════════════════════════════════════════════

    public function getClasses(Request $request)
    {
        $classes = LabClass::where('organization_id', $request->organization_id)
            ->where('is_active', 1)
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($classes);
    }

    // ══════════════════════════════════════════════════════════════════
    // STORE BOOKING
    // ══════════════════════════════════════════════════════════════════

    public function storeBooking(Request $request)
    {
        // Guard — hari Minggu pakai method khusus
        if ($request->filled('booking_date')) {
            if (Carbon::parse($request->booking_date)->format('l') === 'Sunday') {
                return back()->withErrors(['error' => 'Booking hari Minggu menggunakan form khusus.'])->withInput();
            }
        }

        $maxDate = now()->addWeek()->toDateString();

        $request->validate([
            'resource_id'       => 'required|exists:resources,id',
            'time_slot_id'      => 'required|exists:time_slots,id',
            'organization_id'   => 'required|exists:organizations,id',
            'class_id'          => 'required|exists:classes,id',
            'booking_date'      => "required|date|after_or_equal:today|before_or_equal:{$maxDate}",
            'teacher_name'      => 'required|string|max:255',
            'teacher_phone'     => 'required|string|max:50',
            'subject_name'      => 'required|string|max:255',
            'title'             => 'required|string|max:255',
            'description'       => 'nullable|string|max:500',
            'participant_count' => 'required|integer|min:1|max:200',
        ]);

        $dayEn        = Carbon::parse($request->booking_date)->format('l');
        $teacherName  = trim(ucwords(strtolower($request->teacher_name)));
        $teacherPhone = $this->normalizePhone($request->teacher_phone);

        $allSlotIds = $this->resolveSlotIds($request);

        try {
            $result = DB::transaction(function () use ($request, $dayEn, $teacherName, $teacherPhone, $allSlotIds) {

                // Cek slot yang sudah terpakai — query SEKALI dengan lock
                $takenBookingSlots = Booking::where('resource_id', $request->resource_id)
                    ->where('booking_date', $request->booking_date)
                    ->whereIn('status', ['pending', 'approved'])
                    ->whereIn('time_slot_id', $allSlotIds)
                    ->lockForUpdate()
                    ->pluck('time_slot_id')
                    ->toArray();

                $takenScheduleSlots = Schedule::where('resource_id', $request->resource_id)
                    ->where('day_of_week', $dayEn)
                    ->where('status', 'active')
                    ->whereIn('time_slot_id', $allSlotIds)
                    ->pluck('time_slot_id')
                    ->toArray();

                $takenSlotIds = array_unique(array_merge($takenBookingSlots, $takenScheduleSlots));

                // Slot utama harus bebas
                if (in_array((int) $request->time_slot_id, $takenSlotIds)) {
                    throw new \Exception('Slot ini sudah dibooking atau ada jadwal tetap.');
                }

                $labClass    = LabClass::findOrFail($request->class_id);
                $teacher     = $this->upsertTeacher($teacherName, $teacherPhone);

                $bookingData = [
                    'resource_id'       => $request->resource_id,
                    'organization_id'   => $request->organization_id,
                    'booking_date'      => $request->booking_date,
                    'teacher_name'      => $teacherName,
                    'teacher_phone'     => $teacherPhone,
                    'class_name'        => $labClass->name,
                    'subject_name'      => $request->subject_name,
                    'title'             => $request->title,
                    'description'       => $request->description,
                    'participant_count' => $request->participant_count,
                    'status'            => 'pending',
                ];

                $bookedCount  = 0;
                $skippedCount = 0;

                foreach ($allSlotIds as $slotId) {
                    if (in_array((int) $slotId, $takenSlotIds)) {
                        $skippedCount++;
                        continue;
                    }
                    Booking::create(array_merge($bookingData, ['time_slot_id' => $slotId]));
                    $bookedCount++;
                }

                return compact('bookedCount', 'skippedCount', 'labClass', 'teacher');
            });

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }

        // Notif WA — di luar transaction, error tidak block response
        if ($result['bookedCount'] > 0) {
            $this->sendBookingNotification($request, $teacherName, $teacherPhone, $result['labClass']);
        }

        $slotInfo = $result['bookedCount'] . ' slot berhasil';
        if ($result['skippedCount'] > 0) {
            $slotInfo .= ", {$result['skippedCount']} slot dilewati (sudah terpakai)";
        }

        return redirect()->back()
            ->with('success', "Booking berhasil diajukan ({$slotInfo})! Admin akan segera menghubungi via WhatsApp jika disetujui.")
            ->with('week', $request->get('week'));
    }

    // ══════════════════════════════════════════════════════════════════
    // STORE SUNDAY BOOKING
    // ══════════════════════════════════════════════════════════════════

    public function storeSundayBooking(Request $request)
    {
        $maxDate = now()->addWeek()->toDateString();

        $request->validate([
            'resource_id'       => 'required|exists:resources,id',
            'organization_id'   => 'required|exists:organizations,id',
            'class_id'          => 'required|exists:classes,id',
            'booking_date'      => "required|date|after_or_equal:today|before_or_equal:{$maxDate}",
            'teacher_name'      => 'required|string|max:255',
            'teacher_phone'     => 'required|string|max:50',
            'subject_name'      => 'required|string|max:255',
            'title'             => 'required|string|max:255',
            'description'       => 'nullable|string|max:500',
            'participant_count' => 'required|integer|min:1|max:200',
        ]);

        if (Carbon::parse($request->booking_date)->format('l') !== 'Sunday') {
            return back()->withErrors(['error' => 'Form ini hanya untuk hari Minggu.'])->withInput();
        }

        $teacherName  = trim(ucwords(strtolower($request->teacher_name)));
        $teacherPhone = $this->normalizePhone($request->teacher_phone);

        try {
            $sundayBooking = DB::transaction(function () use ($request, $teacherName, $teacherPhone) {

                // Cek double booking dengan lock
                $exists = SundayBooking::where('resource_id', $request->resource_id)
                    ->where('booking_date', $request->booking_date)
                    ->whereIn('status', ['pending', 'approved'])
                    ->lockForUpdate()
                    ->exists();

                if ($exists) {
                    throw new \Exception('Lab ini sudah ada booking di hari Minggu tersebut.');
                }

                $labClass = LabClass::findOrFail($request->class_id);
                $teacher  = $this->upsertTeacher($teacherName, $teacherPhone);

                return SundayBooking::create([
                    'teacher_id'        => $teacher->id,
                    'resource_id'       => $request->resource_id,
                    'organization_id'   => $request->organization_id,
                    'booking_date'      => $request->booking_date,
                    'teacher_name'      => $teacherName,
                    'teacher_phone'     => $teacherPhone,
                    'class_name'        => $labClass->name,
                    'subject_name'      => $request->subject_name,
                    'title'             => $request->title,
                    'description'       => $request->description,
                    'participant_count' => $request->participant_count,
                    'status'            => 'pending',
                ]);
            });

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }

        // Notif WA — di luar transaction
        $this->sendSundayBookingNotification($request, $teacherName, $teacherPhone, $sundayBooking);

        return redirect()->back()
            ->with('success', 'Booking Minggu berhasil diajukan! Admin akan segera menghubungi via WhatsApp jika disetujui.')
            ->with('week', $request->get('week'));
    }

    // ══════════════════════════════════════════════════════════════════
    // PRIVATE HELPERS
    // ══════════════════════════════════════════════════════════════════

    /**
     * Normalisasi nomor HP ke format 62xxxxxxxxx
     */
    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\s+/', '', $phone);
        if (str_starts_with($phone, '0')) {
            return '62' . substr($phone, 1);
        }
        if (str_starts_with($phone, '+')) {
            return ltrim($phone, '+');
        }
        return $phone;
    }

    /**
     * Cari atau buat teacher, update phone kalau berubah
     */
    private function upsertTeacher(string $name, string $phone): Teacher
    {
        $teacher = Teacher::whereRaw('LOWER(name) = ?', [strtolower($name)])->first();

        if (!$teacher) {
            return Teacher::create([
                'name'      => $name,
                'phone'     => $phone,
                'token'     => Teacher::generateUniqueToken(),
                'is_active' => true,
            ]);
        }

        if ($teacher->phone !== $phone && $phone) {
            $teacher->update(['phone' => $phone]);
        }

        return $teacher;
    }

    /**
     * Resolve semua slot IDs dari request (main + extra)
     */
    private function resolveSlotIds(Request $request): array
    {
        $allSlotIds = [(int) $request->time_slot_id];

        if ($request->filled('extra_slot_ids')) {
            $extraIds   = is_array($request->extra_slot_ids)
                ? $request->extra_slot_ids
                : explode(',', $request->extra_slot_ids);
            $allSlotIds = array_merge($allSlotIds, array_map('intval', $extraIds));
        }

        return array_values(array_unique(array_filter($allSlotIds)));
    }

    /**
     * Kirim notif WA booking biasa — error tidak block response
     */
    private function sendBookingNotification(Request $request, string $teacherName, string $teacherPhone, LabClass $labClass): void
    {
        try {
            $newBookings = Booking::where('resource_id', $request->resource_id)
                ->where('booking_date', $request->booking_date)
                ->where('teacher_name', $teacherName)
                ->where('status', 'pending')
                ->with('timeSlot')
                ->orderBy('time_slot_id')
                ->get();

            $slots = $newBookings->map(fn($b) =>
                substr($b->timeSlot->start_time ?? '', 0, 5) . '-' . substr($b->timeSlot->end_time ?? '', 0, 5)
            )->join(', ');

            $lab = Resource::find($request->resource_id);

            Http::timeout(3)->post(config('mikrotik.bot_url') . '/api/webhook/lab-session', [
                'event'         => 'booking_pending',
                'booking_ids'   => $newBookings->pluck('id')->toArray(),
                'teacher_name'  => $teacherName,
                'teacher_phone' => $teacherPhone,
                'lab_name'      => $lab->name ?? '-',
                'booking_date'  => Carbon::parse($request->booking_date)->format('d/m/Y'),
                'slots'         => $slots,
                'class_name'    => $labClass->name,
                'subject_name'  => $request->subject_name,
                'total_slots'   => $newBookings->count(),
            ]);

        } catch (\Exception $e) {
            Log::warning('WA booking notification failed: ' . $e->getMessage());
        }
    }

    /**
     * Kirim notif WA booking Minggu — error tidak block response
     */
    private function sendSundayBookingNotification(Request $request, string $teacherName, string $teacherPhone, SundayBooking $booking): void
    {
        try {
            $lab      = Resource::find($request->resource_id);
            $labClass = LabClass::find($request->class_id);

            Http::timeout(3)->post(config('mikrotik.bot_url') . '/api/webhook/lab-session', [
                'event'         => 'booking_pending',
                'booking_ids'   => [$booking->id],
                'teacher_name'  => $teacherName,
                'teacher_phone' => $teacherPhone,
                'lab_name'      => $lab->name ?? '-',
                'booking_date'  => Carbon::parse($request->booking_date)->format('d/m/Y'),
                'slots'         => 'Seharian (Minggu)',
                'class_name'    => $labClass->name ?? '-',
                'subject_name'  => $request->subject_name,
                'total_slots'   => 1,
            ]);

        } catch (\Exception $e) {
            Log::warning('WA Sunday booking notification failed: ' . $e->getMessage());
        }
    }
}