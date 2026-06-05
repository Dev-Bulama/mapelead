<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Enrollment;
use App\Models\NotificationLog;
use App\Models\Payment;
use Illuminate\Http\Request;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $enrollments = Enrollment::where('user_id', $user->id)
            ->with(['course.instructor.user', 'course.category'])
            ->whereIn('status', ['active', 'completed'])
            ->orderByDesc('enrolled_at')
            ->get();

        $stats = [
            'total_courses'    => $enrollments->count(),
            'completed'        => $enrollments->where('status', 'completed')->count(),
            'in_progress'      => $enrollments->where('status', 'active')->count(),
            'certificates'     => Certificate::where('user_id', $user->id)->count(),
            'avg_progress'     => $enrollments->avg('progress_percent') ?? 0,
        ];

        $notifications = NotificationLog::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('student.dashboard', compact('enrollments', 'stats', 'notifications', 'user'));
    }

    public function certificates()
    {
        $certificates = Certificate::where('user_id', auth()->id())
            ->with('course')
            ->orderByDesc('issued_at')
            ->get();

        $lockedEnrollments = \App\Models\Enrollment::where('user_id', auth()->id())
            ->whereIn('status', ['active'])
            ->whereDoesntHave('certificate')
            ->with('course')
            ->get();

        return view('student.certificates', compact('certificates', 'lockedEnrollments'));
    }

    public function downloadCertificate(int $id)
    {
        $certificate = Certificate::where('user_id', auth()->id())
            ->with(['user', 'course'])
            ->findOrFail($id);

        $settings = \App\Models\CertificateSetting::instance();
        $verificationUrl = route('certificate.verify', $certificate->verification_token ?? '');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.certificate', compact('certificate', 'settings', 'verificationUrl'));
        $pdf->setPaper('A4', 'landscape');

        $filename = 'Certificate-' . str_replace('/', '-', $certificate->certificate_number) . '.pdf';
        return $pdf->download($filename);
    }

    public function payments()
    {
        $payments = Payment::where('user_id', auth()->id())
            ->with(['enrollment.course'])
            ->orderByDesc('created_at')
            ->paginate(10);

        $installmentPlans = \App\Models\InstallmentPlan::where('user_id', auth()->id())
            ->with(['course', 'schedule', 'enrollment'])
            ->whereIn('status', ['active', 'overdue'])
            ->orderByDesc('created_at')
            ->get();

        return view('student.payments', compact('payments', 'installmentPlans'));
    }

    public function notifications()
    {
        $notifications = NotificationLog::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(15);
        NotificationLog::where('user_id', auth()->id())->where('is_read', false)->update(['is_read' => true, 'read_at' => now()]);
        return view('student.notifications', compact('notifications'));
    }

    public function markAllNotificationsRead()
    {
        NotificationLog::where('user_id', auth()->id())->where('is_read', false)->update(['is_read' => true, 'read_at' => now()]);
        return back()->with('success', 'All notifications marked as read.');
    }

    public function markNotificationRead(int $id)
    {
        NotificationLog::where('user_id', auth()->id())->where('id', $id)->update(['is_read' => true, 'read_at' => now()]);
        return response()->json(['success' => true]);
    }
}
