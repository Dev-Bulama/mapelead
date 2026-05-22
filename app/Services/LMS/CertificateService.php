<?php
namespace App\Services\LMS;

use App\Models\AttendanceSettings;
use App\Models\Certificate;
use App\Models\CertificateSetting;
use App\Models\Enrollment;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Support\Str;

class CertificateService
{
    public function isEligible(Enrollment $enrollment): array
    {
        $course     = $enrollment->course;
        $userId     = $enrollment->user_id;
        $courseId   = $enrollment->course_id;
        $reasons    = [];
        $eligible   = true;

        // Check training completion
        $progress = (float) ($enrollment->progress_percent ?? 0);
        if ($progress < 100) {
            $eligible = false;
            $reasons[] = "Course not completed (progress: {$progress}%)";
        }

        // Check attendance threshold
        $attSettings = AttendanceSettings::where('course_id', $courseId)->first();
        if ($attSettings && $attSettings->minimum_percentage > 0) {
            $service = new AttendanceService();
            $pct = $service->getStudentPercentage($userId, $courseId);
            if ($pct < $attSettings->minimum_percentage) {
                $eligible = false;
                $reasons[] = "Attendance below required {$attSettings->minimum_percentage}% (current: {$pct}%)";
            }
        }

        // Check payment completed
        if (in_array($enrollment->payment_status, ['pending', 'overdue', 'failed'])) {
            $eligible = false;
            $reasons[] = 'Payment not completed';
        }

        // Check quiz pass requirement (if any required quizzes for the course)
        $requiredQuizzes = Quiz::where('course_id', $courseId)
            ->where('is_required_for_certificate', true)
            ->get();

        foreach ($requiredQuizzes as $quiz) {
            $passed = QuizAttempt::where('user_id', $userId)
                ->where('quiz_id', $quiz->id)
                ->where('passed', true)
                ->exists();

            if (!$passed) {
                $eligible = false;
                $reasons[] = "Required quiz \"{$quiz->title}\" not passed";
            }
        }

        return ['eligible' => $eligible, 'reasons' => $reasons];
    }

    public function issue(Enrollment $enrollment): Certificate
    {
        $existing = Certificate::where('enrollment_id', $enrollment->id)->first();
        if ($existing) return $existing;

        $verificationToken = Str::random(32);
        $certificateNumber = 'CERT-' . strtoupper(Str::random(8));

        $service = new AttendanceService();
        $attPct  = $service->getStudentPercentage($enrollment->user_id, $enrollment->course_id);

        $avgQuizScore = QuizAttempt::where('user_id', $enrollment->user_id)
            ->whereHas('quiz', fn($q) => $q->where('course_id', $enrollment->course_id))
            ->where('passed', true)
            ->avg('score_percent') ?? 0;

        $cert = Certificate::create([
            'user_id'              => $enrollment->user_id,
            'course_id'            => $enrollment->course_id,
            'enrollment_id'        => $enrollment->id,
            'certificate_number'   => $certificateNumber,
            'verification_token'   => $verificationToken,
            'attendance_percentage'=> round($attPct, 2),
            'quiz_score_average'   => round($avgQuizScore, 2),
            'payment_completed'    => in_array($enrollment->payment_status, ['paid', 'full']),
            'template'             => 'default',
            'issued_at'            => now(),
        ]);

        return $cert;
    }

    public function generateQrCode(Certificate $cert): ?string
    {
        if (!class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
            return null;
        }

        $url  = route('certificate.verify', $cert->verification_token);
        $path = "certificates/qr/{$cert->certificate_number}.png";
        $full = storage_path("app/public/{$path}");

        \Illuminate\Support\Facades\Storage::makeDirectory('public/certificates/qr');

        \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
            ->size(200)
            ->generate($url, $full);

        $cert->update(['qr_code_path' => $path]);
        return $path;
    }

    public function findByToken(string $token): ?Certificate
    {
        return Certificate::where('verification_token', $token)
            ->with(['user', 'course'])
            ->first();
    }
}
