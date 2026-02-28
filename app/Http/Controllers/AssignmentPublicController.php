<?php
namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssignmentPublicController extends Controller
{
    public function index()
    {
        $assignments = Assignment::with('teacher')
            ->where('is_active', true)
            ->orderBy('deadline')
            ->get();

        $organizations = \App\Models\Organization::where('is_active', true)->orderBy('name')->get();
        $classes       = \App\Models\LabClass::where('is_active', true)->orderBy('name')->get();

        return view('assignments.public', compact('assignments', 'organizations', 'classes'));
    }

    public function show(Assignment $assignment)
    {
        if (!$assignment->is_active) abort(404);
        $submissions = $assignment->submissions()->orderByDesc('submitted_at')->get();
        return view('assignments.show', compact('assignment', 'submissions'));
    }

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

        $file     = $request->file('file');
        $ext      = $file->getClientOriginalExtension();
        $origName = $file->getClientOriginalName();
        $size     = round($file->getSize() / 1024, 1) . ' KB';
        $path     = $file->store('submissions/' . $assignment->id, 'local');

        AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'student_name'  => $request->student_name,
            'student_class' => $request->student_class,
            'file_path'     => $path,
            'file_name'     => $origName,
            'file_size'     => $size,
            'file_ext'      => $ext,
            'status'        => 'submitted',
            'submitted_at'  => now(),
        ]);

        return back()->with('success', 'Tugas berhasil dikumpulkan!');
    }
}
