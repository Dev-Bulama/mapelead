<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index(Course $course)
    {
        $assignments = $course->assignments()->get();

        return view('admin.lms.assignments.index', compact('course', 'assignments'));
    }

    public function create(Course $course)
    {
        return view('admin.lms.assignments.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title'                        => 'required|string|max:255',
            'description'                  => 'nullable|string',
            'instructions'                 => 'nullable|string',
            'max_score'                    => 'nullable|numeric|min:0',
            'pass_score'                   => 'nullable|numeric|min:0',
            'allowed_file_types'           => 'nullable|string',
            'max_file_size_mb'             => 'nullable|integer|min:1',
            'max_attempts'                 => 'nullable|integer|min:1',
            'due_date'                     => 'nullable|date',
            'is_required_for_certificate'  => 'boolean',
        ]);

        $validated['course_id']                    = $course->id;
        $validated['max_score']                    = $validated['max_score'] ?? 100;
        $validated['pass_score']                   = $validated['pass_score'] ?? 50;
        $validated['max_file_size_mb']             = $validated['max_file_size_mb'] ?? 10;
        $validated['max_attempts']                 = $validated['max_attempts'] ?? 1;
        $validated['is_required_for_certificate']  = $request->boolean('is_required_for_certificate');

        Assignment::create($validated);

        return redirect()->route('admin.courses.assignments.index', $course)
            ->with('success', 'Assignment created successfully.');
    }

    public function edit(Assignment $assignment)
    {
        return view('admin.lms.assignments.edit', compact('assignment'));
    }

    public function update(Request $request, Assignment $assignment)
    {
        $validated = $request->validate([
            'title'                        => 'required|string|max:255',
            'description'                  => 'nullable|string',
            'instructions'                 => 'nullable|string',
            'max_score'                    => 'nullable|numeric|min:0',
            'pass_score'                   => 'nullable|numeric|min:0',
            'allowed_file_types'           => 'nullable|string',
            'max_file_size_mb'             => 'nullable|integer|min:1',
            'max_attempts'                 => 'nullable|integer|min:1',
            'due_date'                     => 'nullable|date',
            'is_required_for_certificate'  => 'boolean',
        ]);

        $validated['is_required_for_certificate'] = $request->boolean('is_required_for_certificate');

        $assignment->update($validated);

        return redirect()->back()->with('success', 'Assignment updated successfully.');
    }

    public function destroy(Assignment $assignment)
    {
        $assignment->delete();

        return redirect()->back()->with('success', 'Assignment deleted successfully.');
    }

    public function submissions(Assignment $assignment)
    {
        $submissions = $assignment->submissions()
            ->with('user')
            ->paginate(20);

        return view('admin.lms.assignments.submissions', compact('assignment', 'submissions'));
    }

    public function gradeSubmission(Request $request, AssignmentSubmission $submission)
    {
        $assignment = $submission->assignment;

        $validated = $request->validate([
            'score'    => 'required|numeric|min:0|max:' . $assignment->max_score,
            'feedback' => 'nullable|string',
        ]);

        $passed = $validated['score'] >= $assignment->pass_score;

        $submission->update([
            'score'      => $validated['score'],
            'feedback'   => $validated['feedback'] ?? null,
            'status'     => 'graded',
            'graded_at'  => now(),
            'graded_by'  => auth()->id(),
            'passed'     => $passed,
        ]);

        return redirect()->back()->with('success', 'Submission graded successfully.');
    }
}
