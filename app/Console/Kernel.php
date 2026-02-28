<?php
namespace App\Console;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Generate token lab control H-5 menit sebelum jadwal rutin
        $schedule->call(function () {
            $today  = now()->format('l');
            $target = now()->addMinutes(5);
            $todate = now()->toDateString();

            // Kumpulkan semua slot yang akan mulai dalam 5 menit
            $schedules = \App\Models\Schedule::where('status', 'active')
                ->whereNull('deleted_at')
                ->where('day_of_week', $today)
                ->with('timeSlot')
                ->get()
                ->filter(function ($sch) use ($target, $todate) {
                    if (!$sch->timeSlot) return false;
                    $schedStart = \Carbon\Carbon::parse($todate . ' ' . $sch->timeSlot->start_time);
                    return abs($schedStart->diffInMinutes($target)) <= 1;
                });

            if ($schedules->isEmpty()) return;

            // Group by teacher_name + resource_id agar tidak kirim WA 2x
            $groups = $schedules->groupBy(fn($s) => $s->teacher_name . '_' . $s->resource_id);

            foreach ($groups as $group) {
                $session = null;
                foreach ($group as $sch) {
                    $sch->start_time = $sch->timeSlot->start_time;
                    $sch->end_time   = $sch->timeSlot->end_time;
                    $session = \App\Http\Controllers\LabControlController::generateFromSchedule($sch);
                }
                // Kirim WA sekali setelah semua slot di-extend
                if ($session) {
                    (new \App\Http\Controllers\LabControlController)->sendWebhookPublic($session->fresh());
                }
            }
        })->everyMinute()->name('lab-session-generator')->withoutOverlapping();

        // Auto-invalidate sesi yang sudah expired
        $schedule->call(function () {
            \App\Models\LabSession::where('is_active', true)
                ->where('session_end', '<', now())
                ->whereNull('invalidated_at')
                ->update([
                    'is_active'          => false,
                    'invalidated_at'     => now(),
                    'invalidated_reason' => 'expired',
                ]);
        })->everyFiveMinutes()->name('lab-session-cleanup')->withoutOverlapping();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}