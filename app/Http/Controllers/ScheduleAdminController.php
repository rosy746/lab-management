<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resource;
use App\Models\Schedule;
use App\Models\TimeSlot;
use App\Models\Organization;
use App\Models\LabClass;
use App\Models\Teacher;

class ScheduleAdminController extends Controller
{
    private $days = [
        'Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu',
        'Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu','Sunday'=>'Minggu',
    ];

    private function getAllowedResources(): ?array
    {
        $user = auth()->user();
        if (in_array($user->role, ['admin', 'operator'])) return null;
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

    public function index(Request $request)
    {
        $allowed = $this->getAllowedResources();

        $query = Schedule::with(['resource','timeSlot','labClass'])
            ->whereNull('deleted_at');

        if ($allowed !== null) {
            $query->whereIn('resource_id', $allowed);
        }

        $dayOrder = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
        $schedules = $query->get()->sortBy([
            fn($a,$b) => array_search($a->day_of_week, $dayOrder) <=> array_search($b->day_of_week, $dayOrder),
            fn($a,$b) => ($a->timeSlot->slot_order ?? 0) <=> ($b->timeSlot->slot_order ?? 0),
        ])->values();

        // ─── SCHEDULE GRID untuk tampilan grid ───────────────
        // Key: "{resource_id}_{day_of_week}_{time_slot_id}"
        // Selalu load SEMUA jadwal aktif (tanpa filter) agar grid lengkap
        $allSchedules = Schedule::with(['resource','timeSlot','labClass'])
            ->whereNull('deleted_at')
            ->where('status', 'active');
        if ($allowed !== null) $allSchedules->whereIn('resource_id', $allowed);
        $scheduleGrid = $allSchedules->get()->groupBy(function($s) {
            return $s->resource_id . '_' . $s->day_of_week . '_' . $s->time_slot_id;
        });
        // ─────────────────────────────────────────────────────

        $resQuery = Resource::where('status','active')->orderBy('name');
        if ($allowed !== null) $resQuery->whereIn('id', $allowed);
        $resources = $resQuery->get();

        // TimeSlots dengan is_break juga (untuk baris istirahat di grid)
        $timeSlots     = TimeSlot::where('is_active',1)->orderBy('slot_order')->get();
        $timeSlotsForm = $timeSlots->where('is_break', false); // untuk dropdown form

        $organizations = Organization::where('is_active',1)->orderBy('name')->get();
        $teachers      = Teacher::where('is_active',1)->orderBy('name')->get(['id','name','phone']);

        $baseStats = Schedule::whereNull('deleted_at');
        if ($allowed !== null) $baseStats->whereIn('resource_id', $allowed);
        $stats = [
            'total'    => (clone $baseStats)->count(),
            'active'   => (clone $baseStats)->where('status','active')->count(),
            'inactive' => (clone $baseStats)->where('status','inactive')->count(),
        ];

        return view('schedule.admin', compact(
            'scheduleGrid','resources','timeSlots','timeSlotsForm',
            'organizations','teachers','stats'
        ))->with('days', $this->days);
    }

    public function store(Request $request)
    {
        if (!$this->checkResourceAccess((int)$request->resource_id)) {
            return back()->withErrors(['error' => 'Anda tidak memiliki akses ke lab ini.'])->withInput();
        }

        $request->validate([
            'resource_id'  => 'required|exists:resources,id',
            'time_slot_id' => 'required|exists:time_slots,id',
            'class_id'     => 'required|exists:classes,id',
            'day_of_week'  => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'teacher_name' => 'required|string|max:255',
            'subject_name' => 'nullable|string|max:255',
            'notes'        => 'nullable|string',
        ]);

        $exists = Schedule::where('resource_id', $request->resource_id)
            ->where('time_slot_id', $request->time_slot_id)
            ->where('day_of_week', $request->day_of_week)
            ->where('status', 'active')
            ->whereNull('deleted_at')->exists();

        if ($exists) {
            return back()->withErrors(['error' => 'Slot ini sudah ada jadwal tetap yang aktif.'])->withInput();
        }

        Schedule::create([
            'resource_id'  => $request->resource_id,
            'time_slot_id' => $request->time_slot_id,
            'class_id'     => $request->class_id,
            'day_of_week'  => $request->day_of_week,
            'teacher_name' => $request->teacher_name,
            'subject_name' => $request->subject_name,
            'notes'        => $request->notes,
            'status'       => 'active',
            'user_id'      => auth()->id(),
        ]);

        return back()->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function update(Request $request, Schedule $schedule)
    {
        if (!$this->checkResourceAccess($schedule->resource_id)) {
            return back()->with('error', 'Anda tidak memiliki akses ke lab ini.');
        }

        $request->validate([
            'teacher_name' => 'required|string|max:255',
            'subject_name' => 'nullable|string|max:255',
            'notes'        => 'nullable|string',
            'status'       => 'required|in:active,inactive',
        ]);

        $schedule->update([
            'teacher_name' => $request->teacher_name,
            'subject_name' => $request->subject_name,
            'notes'        => $request->notes,
            'status'       => $request->status,
        ]);

        return back()->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Schedule $schedule)
    {
        if (!$this->checkResourceAccess($schedule->resource_id)) {
            return back()->with('error', 'Anda tidak memiliki akses ke lab ini.');
        }
        $schedule->update(['deleted_at' => now()]);
        return back()->with('success', 'Jadwal berhasil dihapus.');
    }

    public function getClassesByOrg(Request $request)
    {
        $classes = LabClass::where('organization_id', $request->organization_id)
            ->where('is_active', 1)->whereNull('deleted_at')
            ->orderBy('name')->get(['id', 'name']);
        return response()->json($classes);
    }
}