<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Resource;
use App\Models\Schedule;
use App\Models\Booking;
use App\Models\TimeSlot;
use Carbon\Carbon;

class RekapPublicController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year  = $request->get('year',  now()->year);

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate   = Carbon::create($year, $month, 1)->endOfMonth();

        // ── Cache data master yang jarang berubah ──
        $resources = Cache::remember('active_resources', 300, function () {
            return Resource::where('status', 'active')
                ->orderBy('name')
                ->get(['id', 'name', 'building', 'capacity', 'status']);
        });

        $timeSlots = Cache::remember('active_time_slots_nonbreak', 3600, function () {
            return TimeSlot::where('is_active', 1)
                ->where('is_break', 0)
                ->orderBy('slot_order')
                ->get();
        });

        $totalSlotPerDay  = $timeSlots->count();
        $totalDaysInMonth = $startDate->daysInMonth;
        $resourceIds      = $resources->pluck('id')->toArray();

        // ── Cache schedules per bulan (jarang berubah) ──
        $allSchedules = Cache::remember('active_schedules', 300, function () use ($resourceIds) {
            return Schedule::whereIn('resource_id', $resourceIds)
                ->where('status', 'active')
                ->whereNull('deleted_at')
                ->with(['timeSlot', 'labClass'])
                ->get()
                ->groupBy('resource_id');
        });

        // ── Bookings TIDAK di-cache — berubah setiap ada booking ──
        $allBookings = Booking::whereIn('resource_id', $resourceIds)
            ->whereBetween('booking_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->where('status', 'approved')
            ->with('timeSlot')
            ->orderBy('booking_date')
            ->get()
            ->groupBy('resource_id');

        $dayMap = [
            'Monday'    => 1, 'Tuesday' => 2, 'Wednesday' => 3,
            'Thursday'  => 4, 'Friday'  => 5, 'Saturday'  => 6, 'Sunday' => 0,
        ];
        $dayNameId = [
            'Monday'    => 'Senin',   'Tuesday'  => 'Selasa', 'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',   'Friday'   => 'Jumat',  'Saturday'  => 'Sabtu',
            'Sunday'    => 'Minggu',
        ];

        // Pre-compute jumlah kemunculan tiap hari dalam bulan
        $dayOccurrences = [];
        for ($d = 1; $d <= $totalDaysInMonth; $d++) {
            $dayName = Carbon::create($year, $month, $d)->format('l');
            $dayOccurrences[$dayName] = ($dayOccurrences[$dayName] ?? 0) + 1;
        }

        $labData = [];

        foreach ($resources as $resource) {
            $schedules      = $allSchedules->get($resource->id, collect());
            $bookingDetails = $allBookings->get($resource->id, collect());

            $scheduledSlots  = 0;
            $scheduleDetails = $schedules->map(function ($sch) use ($dayOccurrences, $dayNameId) {
                $occurrences      = $dayOccurrences[$sch->day_of_week] ?? 0;
                $sch->occurrences = $occurrences;
                $sch->day_name_id = $dayNameId[$sch->day_of_week] ?? $sch->day_of_week;
                return $sch;
            })->filter(fn($s) => $s->occurrences > 0)
              ->sortBy(fn($s) => $dayMap[$s->day_of_week] ?? 9);

            foreach ($scheduleDetails as $sd) {
                $scheduledSlots += $sd->occurrences;
            }

            $bookingSlots  = $bookingDetails->count();
            $totalCapacity = $totalDaysInMonth * $totalSlotPerDay;
            $totalUsed     = $scheduledSlots + $bookingSlots;
            $percentage    = $totalCapacity > 0
                ? round(($totalUsed / $totalCapacity) * 100, 1) : 0;

            $bookingByDate = $bookingDetails->groupBy('booking_date');
            $scheduleByDay = $schedules->groupBy('day_of_week');

            $dailyData = [];
            for ($d = 1; $d <= $totalDaysInMonth; $d++) {
                $date    = Carbon::create($year, $month, $d);
                $dayName = $date->format('l');
                $dateStr = $date->toDateString();

                $schedCount = $scheduleByDay->get($dayName, collect())->count();
                $bookCount  = $bookingByDate->get($dateStr, collect())->count();

                $dailyData[] = [
                    'date'     => $date,
                    'schedule' => $schedCount,
                    'booking'  => $bookCount,
                    'total'    => $schedCount + $bookCount,
                    'capacity' => $totalSlotPerDay,
                    'isSunday' => $date->dayOfWeek === 0,
                    'isToday'  => $date->isToday(),
                ];
            }

            $labData[] = [
                'resource'        => $resource,
                'totalCapacity'   => $totalCapacity,
                'scheduledSlots'  => $scheduledSlots,
                'bookingSlots'    => $bookingSlots,
                'totalUsed'       => $totalUsed,
                'totalFree'       => max(0, $totalCapacity - $totalUsed),
                'percentage'      => $percentage,
                'dailyData'       => $dailyData,
                'scheduleDetails' => $scheduleDetails,
                'bookingDetails'  => $bookingDetails,
            ];
        }

        $summary = [
            'total_capacity'  => collect($labData)->sum('totalCapacity'),
            'total_scheduled' => collect($labData)->sum('scheduledSlots'),
            'total_booking'   => collect($labData)->sum('bookingSlots'),
            'total_used'      => collect($labData)->sum('totalUsed'),
        ];
        $summary['total_pct'] = $summary['total_capacity'] > 0
            ? round(($summary['total_used'] / $summary['total_capacity']) * 100, 1) : 0;

        $months = [
            1  => 'Januari',  2 => 'Februari', 3  => 'Maret',    4  => 'April',
            5  => 'Mei',      6 => 'Juni',      7  => 'Juli',     8  => 'Agustus',
            9  => 'September',10 => 'Oktober',  11 => 'November', 12 => 'Desember',
        ];
        $years = range(now()->year - 1, now()->year + 1);

        return view('rekap.public', compact(
            'labData', 'summary', 'month', 'year',
            'months', 'years', 'startDate', 'endDate', 'totalSlotPerDay'
        ));
    }
}