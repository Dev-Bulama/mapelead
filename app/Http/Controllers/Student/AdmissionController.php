<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\BatchEnrollment;
use App\Models\Certificate;
use App\Models\Enrollment;
use App\Models\CertificateSetting;
use Barryvdh\DomPDF\Facade\Pdf;

class AdmissionController extends Controller
{
    public function index()
    {
        $user = auth()->user()->load('enrollments');

        // Latest active or completed enrollment with full relations
        $enrollment = Enrollment::where('user_id', $user->id)
            ->with([
                'course',
                'course.category',
                'batch.batch',
            ])
            ->whereIn('status', ['active', 'completed'])
            ->orderByDesc('enrolled_at')
            ->first();

        // All enrollments with course for the sidebar list
        $enrollments = Enrollment::where('user_id', $user->id)
            ->with('course')
            ->orderByDesc('enrolled_at')
            ->get();

        return view('student.admission', compact('user', 'enrollment', 'enrollments'));
    }

    public function downloadSlip()
    {
        $user = auth()->user();

        if (! $user->admission_number) {
            return redirect()->back()->with('error', 'You do not have an admission number assigned yet. Please contact the admin.');
        }

        $enrollment = Enrollment::where('user_id', $user->id)
            ->with([
                'course',
                'course.category',
                'batch.batch',
            ])
            ->whereIn('status', ['active', 'completed'])
            ->orderByDesc('enrolled_at')
            ->first();

        $settings = CertificateSetting::instance();

        $verificationUrl = route('admission.verify.public', $user->admission_number);

        $pdf = Pdf::loadView('pdf.admission-slip', compact('user', 'enrollment', 'settings', 'verificationUrl'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('Admission-' . $user->admission_number . '.pdf');
    }
}
