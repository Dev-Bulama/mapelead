<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\User;
use App\Models\BlogPost;
use App\Models\PageView;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users'       => User::count(),
            'new_users_today'   => User::whereDate('created_at', today())->count(),
            'total_students'    => User::students()->count(),
            'total_courses'     => Course::published()->count(),
            'total_enrollments' => Enrollment::count(),
            'total_revenue'     => Payment::where('status', 'success')->sum('amount'),
            'revenue_today'     => Payment::where('status', 'success')->whereDate('paid_at', today())->sum('amount'),
            'pending_leads'     => Lead::where('status', 'new')->count(),
            'total_blog_posts'  => BlogPost::published()->count(),
        ];

        $revenueChart = Payment::where('status', 'success')
            ->where('paid_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(paid_at) as date'), DB::raw('SUM(amount) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $enrollmentChart = Enrollment::where('enrolled_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(enrolled_at) as date'), DB::raw('COUNT(*) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $topCourses = Course::withCount('enrollments')
            ->orderByDesc('enrollments_count')
            ->limit(5)
            ->get();

        $recentEnrollments = Enrollment::with(['user', 'course'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $recentPayments = Payment::with(['user', 'enrollment.course'])
            ->where('status', 'success')
            ->orderByDesc('paid_at')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'stats', 'revenueChart', 'enrollmentChart',
            'topCourses', 'recentEnrollments', 'recentPayments'
        ));
    }

    public function docs()
    {
        return view('admin.docs');
    }
}
