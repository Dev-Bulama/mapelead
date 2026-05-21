<?php
namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;

class InstructorDashboardController extends Controller
{
    public function index()
    {
        $instructor = auth()->user()->instructor;
        if (!$instructor) return redirect()->route('home')->withErrors(['error' => 'Instructor profile not found.']);

        $courses = Course::where('instructor_id', $instructor->id)->withCount('enrollments')->get();
        $totalStudents = $courses->sum('enrollments_count');
        $totalRevenue  = Enrollment::whereIn('course_id', $courses->pluck('id'))
            ->where('payment_status', 'paid')->sum('amount_paid');

        return view('instructor.dashboard', compact('instructor', 'courses', 'totalStudents', 'totalRevenue'));
    }

    public function students()
    {
        $instructor = auth()->user()->instructor;
        $students = Enrollment::whereIn('course_id', Course::where('instructor_id', $instructor->id)->pluck('id'))
            ->with(['user', 'course'])
            ->where('status', 'active')
            ->paginate(20);
        return view('instructor.students', compact('students'));
    }

    public function earnings()
    {
        $instructor = auth()->user()->instructor;
        $payments = \App\Models\Payment::whereHas('enrollment', fn($q) => $q->whereIn('course_id',
            Course::where('instructor_id', $instructor->id)->pluck('id')
        ))->with('enrollment.course')->orderByDesc('paid_at')->paginate(20);
        return view('instructor.earnings', compact('payments'));
    }

    public function courseAnalytics(int $id)
    {
        $course = Course::where('instructor_id', auth()->user()->instructor->id)->findOrFail($id);
        return view('instructor.course-analytics', compact('course'));
    }
}
