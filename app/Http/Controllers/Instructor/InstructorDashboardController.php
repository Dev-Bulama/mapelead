<?php
namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\CourseInstructor;
use App\Models\Enrollment;

class InstructorDashboardController extends Controller
{
    public function index()
    {
        $instructor = auth()->user()->instructor;
        if (!$instructor) return redirect()->route('home')->withErrors(['error' => 'Instructor profile not found.']);

        $courses = Course::where('instructor_id', $instructor->id)->withCount('enrollments')->get();
        $courseIds = $courses->pluck('id');

        $totalStudents    = $courses->sum('enrollments_count');
        $totalEnrollments = Enrollment::whereIn('course_id', $courseIds)->count();
        $totalRevenue     = Enrollment::whereIn('course_id', $courseIds)
            ->where('payment_status', 'paid')->sum('amount_paid');

        $avgProgress = Enrollment::whereIn('course_id', $courseIds)
            ->where('status', 'active')->avg('progress_percent') ?? 0;

        $assignmentIds = Assignment::whereIn('course_id', $courseIds)->pluck('id');
        $pendingReviews = AssignmentSubmission::whereIn('assignment_id', $assignmentIds)
            ->where('status', 'submitted')->count();

        $recentEnrollments = Enrollment::whereIn('course_id', $courseIds)
            ->with(['user', 'course'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $sessionAssignments = CourseInstructor::where('instructor_id', $instructor->id)
            ->with(['course' => fn($q) => $q->withCount('enrollments')->with('category')])
            ->orderBy('sort_order')
            ->get();

        return view('instructor.dashboard', compact(
            'instructor', 'courses', 'totalStudents', 'totalEnrollments',
            'totalRevenue', 'avgProgress', 'pendingReviews',
            'recentEnrollments', 'sessionAssignments'
        ));
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
