<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

        $resources = Resource::where('status', 'active')->orderBy('name')->get();
        $timeSlots = TimeSlot::where('is_active', 1)->where('is_break', 0)
            ->orderBy('slot_order')->get();

        $totalSlotPerDay  = $timeSlots->count();
        $totalDaysInMonth = $startDate->daysInMonth;

        $dayMap = [
            'Monday'=>1,'Tuesday'=>2,'Wednesday'=>3,
            'Thursday'=>4,'Friday'=>5,'Saturday'=>6,'Sunday'=>0
        ];
        $dayNameId = [
            'Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu',
            'Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu','Sunday'=>'Minggu'
        ];

        $labData = [];

        foreach ($resources as $resource) {

            // Jadwal tetap
            $schedules = Schedule::where('resource_id', $resource->id)
                ->where('status', 'active')->whereNull('deleted_at')
                ->with(['timeSlot', 'labClass'])->get();

            $scheduledSlots  = 0;
            $scheduleDetails = $schedules->map(function($sch) use ($dayMap, $totalDaysInMonth, $year, $month, $dayNameId) {
                $dayNum = $dayMap[$sch->day_of_week] ?? null;
                $occurrences = 0;
                if ($dayNum !== null) {
                    for ($d = 1; $d <= $totalDaysInMonth; $d++) {
                        if (Carbon::create($year, $month, $d)->dayOfWeek == $dayNum) {
                            $occurrences++;
                        }
                    }
                }
                $sch->occurrences  = $occurrences;
                $sch->day_name_id  = $dayNameId[$sch->day_of_week] ?? $sch->day_of_week;
                return $sch;
            })->filter(fn($s) => $s->occurrences > 0)
              ->sortBy(fn($s) => $dayMap[$s->day_of_week] ?? 9);

            foreach ($scheduleDetails as $sd) {
                $scheduledSlots += $sd->occurrences;
            }

            // Booking approved
            $bookingDetails = Booking::where('resource_id', $resource->id)
                ->whereBetween('booking_date', [$startDate, $endDate])
                ->where('status', 'approved')
                ->with('timeSlot')
                ->orderBy('booking_date')
                ->get();

            $bookingSlots  = $bookingDetails->count();
            $totalCapacity = $totalDaysInMonth * $totalSlotPerDay;
            $totalUsed     = $scheduledSlots + $bookingSlots;
            $percentage    = $totalCapacity > 0
                ? round(($totalUsed / $totalCapacity) * 100, 1) : 0;

            // Daily calendar data
            $dailyData = [];
            for ($d = 1; $d <= $totalDaysInMonth; $d++) {
                $date    = Carbon::create($year, $month, $d);
                $dayName = $date->format('l');
                $dateStr = $date->toDateString();

                $schedCount = $schedules->filter(fn($s) => $s->day_of_week === $dayName)->count();
                $bookCount  = $bookingDetails->filter(fn($b) => $b->booking_date === $dateStr)->count();

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
            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',
            5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',
            9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
        ];
        $years = range(now()->year - 1, now()->year + 1);

        return view('rekap.public', compact(
            'labData','summary','month','year',
            'months','years','startDate','endDate','totalSlotPerDay'
        ));
    }
}