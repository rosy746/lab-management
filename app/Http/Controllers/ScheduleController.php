<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resource;
use App\Models\Schedule;
use App\Models\TimeSlot;
use App\Models\Booking;
use App\Models\Organization;
use App\Models\LabClass;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    private $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
    private $dayMapReverse = [
        'Senin'=>'Monday','Selasa'=>'Tuesday','Rabu'=>'Wednesday',
        'Kamis'=>'Thursday','Jumat'=>'Friday','Sabtu'=>'Saturday','Minggu'=>'Sunday',
    ];

    public function index(Request $request)
    {
        $weekStart = $request->get('week')
            ? Carbon::parse($request->get('week'))->startOfWeek(Carbon::MONDAY)
            : Carbon::now()->startOfWeek(Carbon::MONDAY);

        $weekEnd = $weekStart->copy()->addDays(6);

        $resources     = Resource::where('status', 'active')->orderBy('name')->get();
        $timeSlots     = TimeSlot::where('is_active', 1)->orderBy('slot_order')->get();
        $organizations = Organization::where('is_active', 1)->orderBy('name')->get();

        $schedules = Schedule::with(['labClass'])
            ->where('status', 'active')
            ->get()
            ->groupBy(fn($s) => $s->resource_id . '_' . $s->day_of_week . '_' . $s->time_slot_id);

        $bookings = Booking::whereBetween('booking_date', [$weekStart, $weekEnd])
            ->whereIn('status', ['pending', 'approved'])
            ->get()
            ->groupBy(fn($b) => $b->resource_id . '_' . Carbon::parse($b->booking_date)->toDateString() . '_' . $b->time_slot_id);

        $weekDates = [];
        foreach ($this->days as $i => $day) {
            $weekDates[$day] = $weekStart->copy()->addDays($i)->toDateString();
        }

        $prevWeek = $weekStart->copy()->subWeek()->toDateString();
        $nextWeek = $weekStart->copy()->addWeek()->toDateString();

        $teachers = \App\Models\Teacher::where('is_active', true)->orderBy('name')->get(['id','name','phone']);

        return view('schedule.index', compact(
            'resources', 'timeSlots', 'schedules', 'bookings',
            'weekDates', 'weekStart', 'weekEnd', 'organizations',
            'prevWeek', 'nextWeek', 'teachers'
        ))->with('days', $this->days)->with('dayMapReverse', $this->dayMapReverse);
    }

    public function getClasses(Request $request)
    {
        $classes = LabClass::where('organization_id', $request->organization_id)
            ->where('is_active', 1)
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($classes);
    }

    public function storeBooking(Request $request)
    {
        $validated = $request->validate([
            'resource_id'       => 'required|exists:resources,id',
            'time_slot_id'      => 'required|exists:time_slots,id',
            'organization_id'   => 'required|exists:organizations,id',
            'class_id'          => 'required|exists:classes,id',
            'booking_date'      => 'required|date|after_or_equal:today',
            'teacher_name'      => 'required|string|max:255',
            'teacher_phone'     => 'required|string|max:50',
            'subject_name'      => 'required|string|max:255',
            'title'             => 'required|string|max:255',
            'description'       => 'nullable|string',
            'participant_count' => 'required|integer|min:1',
        ]);

        $dayEn = Carbon::parse($request->booking_date)->format('l');

        // Cek jadwal tetap untuk slot pertama
        $hasSchedule = Schedule::where('resource_id', $request->resource_id)
            ->where('time_slot_id', $request->time_slot_id)
            ->where('day_of_week', $dayEn)
            ->where('status', 'active')
            ->exists();

        if ($hasSchedule) {
            return back()->withErrors(['error' => 'Slot ini sudah ada jadwal tetap.'])->withInput();
        }

        // Cek booking existing untuk slot pertama
        $hasBooking = Booking::where('resource_id', $request->resource_id)
            ->where('time_slot_id', $request->time_slot_id)
            ->where('booking_date', $request->booking_date)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($hasBooking) {
            return back()->withErrors(['error' => 'Slot ini sudah dibooking.'])->withInput();
        }

        // Hari Minggu: hanya 1 booking per lab per hari
        if ($dayEn === 'Sunday') {
            $sundayBookingExists = Booking::where('resource_id', $request->resource_id)
                ->where('booking_date', $request->booking_date)
                ->whereIn('status', ['pending', 'approved'])
                ->exists();

            if ($sundayBookingExists) {
                return back()->withErrors(['error' => 'Hari Minggu hanya diperbolehkan 1 booking per laboratorium. Lab ini sudah ada booking di hari ini.'])->withInput();
            }
        }

        $labClass = LabClass::find($request->class_id);

        // Normalisasi nama guru (Title Case)
        $teacherName = trim(ucwords(strtolower($request->teacher_name)));

        // Normalisasi nomor HP: 08xxx → 628xxx
        $teacherPhone = preg_replace('/\s+/', '', $request->teacher_phone);
        if (str_starts_with($teacherPhone, '0')) {
            $teacherPhone = '62' . substr($teacherPhone, 1);
        } elseif (str_starts_with($teacherPhone, '+')) {
            $teacherPhone = ltrim($teacherPhone, '+');
        }

        // Auto-simpan guru ke tabel teachers jika belum ada
        $teacher = \App\Models\Teacher::whereRaw('LOWER(name) = ?', [strtolower($teacherName)])->first();
        if (!$teacher) {
            $teacher = \App\Models\Teacher::create([
                'name'      => $teacherName,
                'phone'     => $teacherPhone,
                'token'     => \App\Models\Teacher::generateUniqueToken(),
                'is_active' => true,
            ]);
        }
        if ($teacher->phone !== $teacherPhone && $teacherPhone) {
            $teacher->update(['phone' => $teacherPhone]);
        }

        $bookingData = [
            'resource_id'       => $request->resource_id,
            'organization_id'   => $request->organization_id,
            'booking_date'      => $request->booking_date,
            'teacher_name'      => $teacherName,
            'teacher_phone'     => $teacherPhone,
            'class_name'        => $labClass->name ?? '',
            'subject_name'      => $request->subject_name,
            'title'             => $request->title,
            'description'       => $request->description,
            'participant_count' => $request->participant_count,
            'status'            => 'pending',
        ];

        // Kumpulkan semua slot yang dipilih
        $allSlotIds = [$request->time_slot_id];
        if ($request->filled('extra_slot_ids')) {
            $extraIds = is_array($request->extra_slot_ids)
                ? $request->extra_slot_ids
                : explode(',', $request->extra_slot_ids);
            $allSlotIds = array_merge($allSlotIds, array_map('intval', $extraIds));
        }
        $allSlotIds = array_unique(array_filter($allSlotIds));

        $bookedCount  = 0;
        $skippedCount = 0;

        foreach ($allSlotIds as $slotId) {
            $slotTaken = Booking::where('resource_id', $request->resource_id)
                ->where('time_slot_id', $slotId)
                ->where('booking_date', $request->booking_date)
                ->whereIn('status', ['pending', 'approved'])
                ->exists();

            $slotScheduled = Schedule::where('resource_id', $request->resource_id)
                ->where('time_slot_id', $slotId)
                ->where('day_of_week', $dayEn)
                ->where('status', 'active')
                ->exists();

            if (!$slotTaken && !$slotScheduled) {
                Booking::create(array_merge($bookingData, ['time_slot_id' => $slotId]));
                $bookedCount++;
            } else {
                $skippedCount++;
            }
        }

        $slotInfo = $bookedCount . ' slot berhasil';
        if ($skippedCount > 0) {
            $slotInfo .= ", {$skippedCount} slot dilewati (sudah terpakai)";
        }

        return redirect()->back()
            ->with('success', "Booking berhasil diajukan ({$slotInfo})! Menunggu persetujuan admin.")
            ->with('week', $request->get('week'));
    }
}