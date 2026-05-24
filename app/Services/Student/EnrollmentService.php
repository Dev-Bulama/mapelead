<?php
namespace App\Services\Student;

use App\Models\Course;
use App\Models\Coupon;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EnrollmentService
{
    public function checkEnrollment(User $user, Course $course): ?Enrollment
    {
        return Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->whereIn('status', ['active', 'completed'])
            ->first();
    }

    public function enroll(User $user, Course $course, array $options = []): Enrollment
    {
        return DB::transaction(function () use ($user, $course, $options) {
            $enrollment = Enrollment::create([
                'user_id'        => $user->id,
                'course_id'      => $course->id,
                'status'         => $course->is_free ? 'active' : 'pending',
                'payment_status' => $course->is_free ? 'paid' : 'unpaid',
                'payment_type'   => $options['payment_type'] ?? 'full',
                'training_type'  => $options['training_type'] ?? 'online',
                'amount_paid'    => $options['amount'] ?? 0,
                'coupon_id'      => $options['coupon_id'] ?? null,
                'discount_amount'=> $options['discount'] ?? 0,
                'enrolled_at'    => now(),
            ]);

            if ($course->is_free) {
                Course::where('id', $course->id)->increment('total_students');
            }

            return $enrollment;
        });
    }

    public function getStudentEnrollments(User $user)
    {
        return Enrollment::where('user_id', $user->id)
            ->with(['course.instructor.user', 'course.category'])
            ->orderByDesc('enrolled_at')
            ->get();
    }

    public function updateProgress(User $user, int $courseId): void
    {
        $totalLessons = \App\Models\Lesson::where('course_id', $courseId)->where('is_published', true)->count();
        if ($totalLessons === 0) return;

        $completedLessons = \App\Models\StudentProgress::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->where('is_completed', true)
            ->count();

        $percent = (int) round(($completedLessons / $totalLessons) * 100);

        Enrollment::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->update([
                'progress_percent' => $percent,
                'status' => $percent === 100 ? 'completed' : 'active',
                'completed_at' => $percent === 100 ? now() : null,
            ]);
    }
}
