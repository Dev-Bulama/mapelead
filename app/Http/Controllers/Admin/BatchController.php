<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\BatchEnrollment;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    public function index()
    {
        $batches = Batch::with('course')->paginate(20);

        return view('admin.lms.batches.index', compact('batches'));
    }

    public function create()
    {
        $courses = Course::where('status', 'active')->get();

        return view('admin.lms.batches.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'course_id'    => 'required|exists:courses,id',
            'start_date'   => 'required|date',
            'end_date'     => 'nullable|date|after:start_date',
            'max_students' => 'nullable|integer|min:1',
            'status'       => 'required|in:active,inactive,completed',
        ]);

        Batch::create($validated);

        return redirect()->route('admin.batches.index')
            ->with('success', 'Batch created successfully.');
    }

    public function edit(Batch $batch)
    {
        $courses = Course::where('status', 'active')->get();

        return view('admin.lms.batches.edit', compact('batch', 'courses'));
    }

    public function update(Request $request, Batch $batch)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'course_id'    => 'required|exists:courses,id',
            'start_date'   => 'required|date',
            'end_date'     => 'nullable|date|after:start_date',
            'max_students' => 'nullable|integer|min:1',
            'status'       => 'required|in:active,inactive,completed',
        ]);

        $batch->update($validated);

        return redirect()->back()->with('success', 'Batch updated successfully.');
    }

    public function destroy(Batch $batch)
    {
        $batch->delete();

        return redirect()->back()->with('success', 'Batch deleted successfully.');
    }

    public function enrollments(Batch $batch)
    {
        $batch->load('batchEnrollments.enrollment.user');

        return view('admin.lms.batches.enrollments', compact('batch'));
    }

    public function addStudent(Request $request, Batch $batch)
    {
        $validated = $request->validate([
            'enrollment_id' => 'required|exists:enrollments,id',
        ]);

        $enrollment = Enrollment::findOrFail($validated['enrollment_id']);

        BatchEnrollment::firstOrCreate([
            'batch_id'      => $batch->id,
            'enrollment_id' => $enrollment->id,
        ]);

        return redirect()->back()->with('success', 'Student added to batch successfully.');
    }

    public function removeStudent(BatchEnrollment $batchEnrollment)
    {
        $batchEnrollment->delete();

        return redirect()->back()->with('success', 'Student removed from batch successfully.');
    }
}
