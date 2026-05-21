<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\PageView;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        $period = request('period', 30);

        $pageViews = PageView::where('created_at', '>=', now()->subDays($period))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as views'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $topPages = PageView::where('created_at', '>=', now()->subDays($period))
            ->select('url', DB::raw('COUNT(*) as views'))
            ->groupBy('url')
            ->orderByDesc('views')
            ->limit(10)
            ->get();

        $deviceBreakdown = PageView::where('created_at', '>=', now()->subDays($period))
            ->select('device', DB::raw('COUNT(*) as count'))
            ->groupBy('device')
            ->get();

        $newUsers = User::where('created_at', '>=', now()->subDays($period))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.analytics.index', compact('pageViews', 'topPages', 'deviceBreakdown', 'newUsers', 'period'));
    }

    public function revenue()
    {
        $period = request('period', 30);

        $revenue = Payment::where('status', 'success')
            ->where('paid_at', '>=', now()->subDays($period))
            ->select(DB::raw('DATE(paid_at) as date'), DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $totalRevenue = Payment::where('status', 'success')
            ->where('paid_at', '>=', now()->subDays($period))
            ->sum('amount');

        $topCourses = Enrollment::where('payment_status', 'paid')
            ->where('enrolled_at', '>=', now()->subDays($period))
            ->select('course_id', DB::raw('SUM(amount_paid) as revenue'), DB::raw('COUNT(*) as enrollments'))
            ->groupBy('course_id')
            ->with('course:id,title')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        return view('admin.analytics.revenue', compact('revenue', 'totalRevenue', 'topCourses', 'period'));
    }
}
