<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Organization;
use App\Models\LabClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class AssignmentPublicController extends Controller
{
    // ══════════════════════════════════════════════════════════════════
    // INDEX
    // ══════════════════════════════════════════════════════════════════

    public function index()
    {
        $organizations = Cache::remember('active_organizations', 3600, function () {
            return Organization::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']);
        });

        $classes = Cache::remember('active_classes', 3600, function () {
            return LabClass::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'organization_id']);
        });

        // ── FIX: tambah withCount('submissions') ──────────────────────
        // Sebelum: $a->submissions->count() di blade → query per assignment (N+1)
        // Sesudah: $a->submissions_count sudah tersedia → 0 query tambahan
        $assignments = Cache::remember('active_assignments', 120, function () {
            return Assignment::with(['teacher:id,name'])
                ->withCount('submissions')          // ← satu LEFT JOIN, bukan N query
                ->where('is_active', true)
                ->orderBy('deadline')
                ->get([
                    'id', 'title', 'description', 'deadline',
                    'teacher_id', 'organization_id', 'is_active',
                    'attachment_path', 'attachment_name',
                ]);
        });

        return view('assignments.public', compact('assignments', 'organizations', 'classes'));
    }

    // ══════════════════════════════════════════════════════════════════
    // SHOW
    // ══════════════════════════════════════════════════════════════════

    public function show(Assignment $assignment)
    {
        if (!$assignment->is_active) abort(404);

        $submissions = $assignment->submissions()
            ->orderByDesc('submitted_at')
            ->get([
                'id', 'assignment_id', 'student_name', 'student_class',
                'file_name', 'file_size', 'file_ext', 'status', 'submitted_at',
            ]);

        return view('assignments.show', compact('assignment', 'submissions'));
    }

    // ══════════════════════════════════════════════════════════════════
    // SUBMIT
    // ══════════════════════════════════════════════════════════════════

    public function submit(Request $request, Assignment $assignment)
    {
        if ($assignment->isExpired()) {
            return back()->withErrors(['error' => 'Deadline sudah lewat.']);
        }

        $request->validate([
            'student_name'  => 'required|string|max:100',
            'student_class' => 'required|string|max:50',
            'file'          => 'required|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar|max:10240',
        ], [
            'file.mimes' => 'File harus berformat PDF, Word, PowerPoint, Excel, ZIP, atau RAR.',
            'file.max'   => 'Ukuran file maksimal 10MB.',
        ]);

        $file = $request->file('file');
        $path = $file->store('submissions/' . $assignment->id, 'local');

        AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'student_name'  => $request->student_name,
            'student_class' => $request->student_class,
            'file_path'     => $path,
            'file_name'     => $file->getClientOriginalName(),
            'file_size'     => round($file->getSize() / 1024, 1) . ' KB',
            'file_ext'      => $file->getClientOriginalExtension(),
            'status'        => 'submitted',
            'submitted_at'  => now(),
        ]);

        Cache::forget('active_assignments');

        return back()->with('success', 'Tugas berhasil dikumpulkan!');
    }
}