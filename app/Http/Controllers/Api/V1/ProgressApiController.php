<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Enrollment;
use App\Models\LearningStreak;
use App\Models\StudentProgress;
use App\Services\Student\EnrollmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProgressApiController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private readonly EnrollmentService $enrollmentService) {}

    public function courseProgress(Request $request, int $courseId): JsonResponse
    {
        $user = $request->user();

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->where('payment_status', 'paid')
            ->with('course.modules.lessons')
            ->first();

        if (!$enrollment) {
            return $this->error('Enrollment not found', 404);
        }

        $completedLessonIds = StudentProgress::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->where('is_completed', true)
            ->pluck('lesson_id')
            ->toArray();

        $modules = $enrollment->course->modules ?? collect();

        return $this->success([
            'course_id'        => $courseId,
            'progress_percent' => (float) $enrollment->progress_percent,
            'status'           => $enrollment->status,
            'completed_at'     => $enrollment->completed_at?->toDateTimeString(),
            'modules'          => $modules->map(fn($m) => [
                'id'         => $m->id,
                'title'      => $m->title,
                'lessons'    => $m->lessons->map(fn($l) => [
                    'id'           => $l->id,
                    'title'        => $l->title,
                    'is_completed' => in_array($l->id, $completedLessonIds),
                ])->values(),
                'completed'  => $m->lessons->every(fn($l) => in_array($l->id, $completedLessonIds)),
            ])->values(),
        ]);
    }

    public function trackStreak(Request $request): JsonResponse
    {
        $request->validate(['minutes_studied' => 'required|integer|min:1']);

        $user  = $request->user();
        $today = now()->toDateString();

        $streak = LearningStreak::updateOrCreate(
            ['user_id' => $user->id, 'streak_date' => $today],
            ['minutes_studied' => DB::raw('minutes_studied + ' . (int) $request->minutes_studied)]
        );

        return $this->success([
            'date'            => $today,
            'minutes_studied' => $streak->minutes_studied,
        ], message: 'Streak updated');
    }

    public function streaks(Request $request): JsonResponse
    {
        $user = $request->user();

        $streaks = LearningStreak::where('user_id', $user->id)
            ->orderByDesc('streak_date')
            ->take(30)
            ->get(['streak_date', 'minutes_studied']);

        // Calculate current streak
        $currentStreak = 0;
        $today         = now()->toDateString();
        $streakDates   = $streaks->pluck('streak_date')->toArray();

        for ($i = 0; $i < count($streakDates); $i++) {
            $expectedDate = now()->subDays($i)->toDateString();
            if (in_array($expectedDate, $streakDates)) {
                $currentStreak++;
            } else {
                break;
            }
        }

        return $this->success([
            'current_streak' => $currentStreak,
            'history'        => $streaks->map(fn($s) => [
                'date'           => $s->streak_date,
                'minutes_studied'=> $s->minutes_studied,
            ])->values(),
        ]);
    }
}
