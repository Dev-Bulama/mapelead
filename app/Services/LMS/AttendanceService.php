<?php
namespace App\Services\LMS;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\AttendanceSettings;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\NotificationLog;
use App\Models\User;

class AttendanceService
{
    public function markBulk(AttendanceSession $session, array $records): void
    {
        foreach ($records as $userId => $status) {
            $enrollment = Enrollment::where('user_id', $userId)
                ->where('course_id', $session->course_id)
                ->first();

            if (!$enrollment) continue;

            AttendanceRecord::updateOrCreate(
                ['session_id' => $session->id, 'user_id' => $userId],
                [
                    'enrollment_id' => $enrollment->id,
                    'status'        => $status,
                    'marked_by'     => auth()->id(),
                ]
            );
        }

        $this->checkThresholds($session->course_id);
    }

    public function getStudentPercentage(int $userId, int $courseId): float
    {
        $totalSessions = AttendanceSession::where('course_id', $courseId)->count();
        if ($totalSessions === 0) return 100.0;

        $present = AttendanceRecord::where('user_id', $userId)
            ->whereHas('session', fn($q) => $q->where('course_id', $courseId))
            ->whereIn('status', ['present', 'late'])
            ->count();

        return round(($present / $totalSessions) * 100, 2);
    }

    protected function checkThresholds(int $courseId): void
    {
        $settings = \App\Models\AttendanceSettings::where('course_id', $courseId)->first();
        if (!$settings) return;

        $enrollments = Enrollment::where('course_id', $courseId)->where('status', 'active')->with('user')->get();

        foreach ($enrollments as $enrollment) {
            $pct = $this->getStudentPercentage($enrollment->user_id, $courseId);

            if ($pct < $settings->notify_threshold) {
                if ($settings->notify_student_below_threshold) {
                    NotificationLog::create([
                        'user_id' => $enrollment->user_id,
                        'title'   => 'Attendance Warning',
                        'message' => "Your attendance is {$pct}%. Minimum required is {$settings->minimum_percentage}%.",
                        'type'    => 'warning',
                    ]);
                }
            }
        }
    }
}
