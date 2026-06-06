<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Instructor;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\CourseInstructor;

class InstructorManagementController extends Controller
{
    public function index()
    {
        $instructors = Instructor::with(['user', 'courses' => fn($q) => $q->withCount('enrollments')])
            ->withCount(['courses', 'sessionAssignments'])
            ->whereHas('user')
            ->orderByDesc('total_students')
            ->paginate(20);

        $globalStats = [
            'total'    => Instructor::count(),
            'verified' => Instructor::where('is_verified', true)->count(),
            'featured' => Instructor::where('is_featured', true)->count(),
            'sessions' => CourseInstructor::count(),
        ];

        return view('admin.instructors.index', compact('instructors', 'globalStats'));
    }

    public function show(Instructor $instructor)
    {
        $instructor->load([
            'user',
            'courses'              => fn($q) => $q->withCount('enrollments')->with('category'),
            'sessionAssignments'   => fn($q) => $q->with('course.category')->orderBy('sort_order'),
        ]);

        $totalStudents = $instructor->courses->sum('enrollments_count');
        $totalRevenue  = Enrollment::whereIn('course_id', $instructor->courses->pluck('id'))
            ->where('payment_status', 'paid')
            ->sum('amount_paid');

        $sessionsBySession = $instructor->sessionAssignments->groupBy('session');

        return view('admin.instructors.show', compact(
            'instructor', 'totalStudents', 'totalRevenue', 'sessionsBySession'
        ));
    }

    public function toggleVerified(Instructor $instructor)
    {
        $instructor->update(['is_verified' => !$instructor->is_verified]);
        return back()->with('success', 'Verification status updated.');
    }

    public function toggleFeatured(Instructor $instructor)
    {
        $instructor->update(['is_featured' => !$instructor->is_featured]);
        return back()->with('success', 'Featured status updated.');
    }
}
