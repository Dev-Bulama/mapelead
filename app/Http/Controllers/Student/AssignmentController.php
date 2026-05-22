<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function show(Assignment $assignment)
    {
        $enrollment = Enrollment::where('user_id', auth()->id())
            ->where('course_id', $assignment->course_id)
            ->first();

        $mySubmission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('user_id', auth()->id())
            ->latest()
            ->first();

        return view('student.assignment.show', compact('assignment', 'enrollment', 'mySubmission'));
    }

    public function submit(Request $request, Assignment $assignment)
    {
        $request->validate([
            'notes' => 'nullable|string',
            'file'  => 'nullable|file|max:20480',
        ]);

        $enrollment = Enrollment::where('user_id', auth()->id())
            ->where('course_id', $assignment->course_id)
            ->where('status', 'active')
            ->firstOrFail();

        $submissionCount = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('user_id', auth()->id())
            ->count();

        if ($submissionCount >= $assignment->max_attempts) {
            return redirect()->back()->with('error', 'You have reached the maximum number of submissions for this assignment.');
        }

        $filePath = null;
        $originalFilename = null;
        $fileSizeBytes = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('assignments/submissions', 'public');
            $originalFilename = $file->getClientOriginalName();
            $fileSizeBytes = $file->getSize();
        }

        AssignmentSubmission::create([
            'assignment_id'     => $assignment->id,
            'user_id'           => auth()->id(),
            'enrollment_id'     => $enrollment->id,
            'notes'             => $request->notes,
            'file_path'         => $filePath,
            'original_filename' => $originalFilename,
            'file_size_bytes'   => $fileSizeBytes,
            'status'            => 'submitted',
            'submitted_at'      => now(),
        ]);

        return redirect()->back()->with('success', 'Assignment submitted successfully.');
    }
}
