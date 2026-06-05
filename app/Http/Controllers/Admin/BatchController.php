<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\BatchEnrollment;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
            'name'         => [
                'required', 'string', 'max:255',
                'regex:/^Cohort\s+\d+(\s*[-–]\s*.+)?$/i',
                'unique:batches,name',
            ],
            'code'         => 'nullable|string|max:50|unique:batches,code',
            'course_id'    => 'required|exists:courses,id',
            'description'  => 'nullable|string|max:500',
            'start_date'   => 'required|date',
            'end_date'     => 'nullable|date|after:start_date',
            'max_students' => 'nullable|integer|min:1',
            'status'       => 'required|in:active,upcoming,inactive,completed',
        ], [
            'name.regex'  => 'Batch name must follow the format "Cohort N" (e.g. "Cohort 1", "Cohort 12"). No duplicates or random strings allowed.',
            'name.unique' => 'This cohort name already exists. Each cohort must have a unique number.',
        ]);

        // Auto-generate code if not provided
        if (empty($validated['code'])) {
            $courseCode = \App\Models\Course::where('id', $validated['course_id'])->value('slug');
            $num        = preg_replace('/[^0-9]/', '', $validated['name']);
            $validated['code'] = strtoupper(Str::substr($courseCode ?? 'BATCH', 0, 4)) . '-C' . str_pad($num, 2, '0', STR_PAD_LEFT);
        }

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
            'name'         => [
                'required', 'string', 'max:255',
                'regex:/^Cohort\s+\d+(\s*[-–]\s*.+)?$/i',
                'unique:batches,name,' . $batch->id,
            ],
            'code'         => 'nullable|string|max:50|unique:batches,code,' . $batch->id,
            'course_id'    => 'required|exists:courses,id',
            'description'  => 'nullable|string|max:500',
            'start_date'   => 'required|date',
            'end_date'     => 'nullable|date|after:start_date',
            'max_students' => 'nullable|integer|min:1',
            'status'       => 'required|in:active,upcoming,inactive,completed',
        ], [
            'name.regex'  => 'Batch name must follow the format "Cohort N" (e.g. "Cohort 1", "Cohort 12").',
            'name.unique' => 'This cohort name already exists.',
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
