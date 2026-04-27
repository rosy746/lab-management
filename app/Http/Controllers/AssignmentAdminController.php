<?php
namespace App\Http\Controllers;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssignmentAdminController extends Controller
{
    public function index(Request $request)
    {
        $token   = $request->query('token') ?? session('teacher_token');
        $teacher = null;
        if ($token) {
            $teacher = Teacher::where('token', strtoupper($token))
                ->where('is_active', true)
                ->first();
            if ($teacher) session(['teacher_token' => $token]);
        }
        if (!$teacher && !auth()->check()) {
            return view('assignments.verify-token');
        }
        if (auth()->check() && in_array(auth()->user()->role, ['admin', 'operator'])) {
            $assignments = Assignment::with(['teacher', 'submissions'])
                ->orderByDesc('created_at')->get();
        } else {
            $assignments = Assignment::with('submissions')
                ->where('teacher_id', $teacher->id)
                ->orderByDesc('created_at')->get();
        }
        $organizations = \App\Models\Organization::where('is_active', true)->orderBy('name')->get();
        $classes       = \App\Models\LabClass::where('is_active', true)->orderBy('name')->get();
        return view('assignments.admin', compact('assignments', 'teacher', 'organizations', 'classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'teacher_token'   => 'required|string',
            'title'           => 'required|string|max:200',
            'description'     => 'nullable|string',
            'subject_name'    => 'required|string|max:100',
            'class_names'     => 'required|array|min:1',
            'deadline'        => 'required|date|after:now',
            'attachment'      => 'nullable|file|max:20480',
            'organization_id' => 'required|exists:organizations,id',
        ], [
            'class_names.required' => 'Pilih minimal satu kelas.',
        ]);

        $teacher = Teacher::where('token', strtoupper($request->teacher_token))
            ->where('is_active', true)
            ->firstOrFail();

        $attachmentPath = null;
        $attachmentName = null;
        $attachmentSize = null;
        if ($request->hasFile('attachment')) {
            $file           = $request->file('attachment');
            $attachmentPath = $file->store('attachments', 'local');
            $attachmentName = $file->getClientOriginalName();
            $attachmentSize = round($file->getSize() / 1024, 1) . ' KB';
        }

        // Buat satu record tugas untuk setiap kelas yang dipilih
        foreach ($request->class_names as $className) {
            Assignment::create([
                'teacher_id'      => $teacher->id,
                'title'           => $request->title,
                'description'     => $request->description,
                'subject_name'    => $request->subject_name,
                'class_name'      => $className,
                'deadline'        => $request->deadline,
                'is_active'       => true,
                'organization_id' => $request->organization_id,
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
                'attachment_size' => $attachmentSize,
            ]);
        }

        return back()->with('success', count($request->class_names) . ' Tugas berhasil dibuat.');
    }

    public function destroy(Assignment $assignment)
    {
        $assignment->submissions->each(function ($sub) {
            Storage::delete($sub->file_path);
        });
        if ($assignment->attachment_path) {
            Storage::delete($assignment->attachment_path);
        }
        $assignment->delete();
        return back()->with('success', 'Tugas dihapus.');
    }

    public function downloadAttachment(Assignment $assignment)
    {
        if (!$assignment->attachment_path) abort(404);
        return Storage::download($assignment->attachment_path, $assignment->attachment_name);
    }

    public function downloadSubmission(AssignmentSubmission $submission)
    {
        return Storage::download($submission->file_path, $submission->file_name);
    }

    public function gradeSubmission(Request $request, AssignmentSubmission $submission)
    {
        $request->validate([
            'grade'    => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string|max:500',
        ]);
        $submission->update([
            'grade'    => $request->grade,
            'feedback' => $request->feedback,
            'status'   => 'graded',
        ]);
        return back()->with('success', 'Nilai berhasil disimpan.');
    }
}