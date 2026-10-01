<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Bookmark;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Note;
use App\Models\StudentProgress;
use App\Services\Student\EnrollmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LessonApiController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private readonly EnrollmentService $enrollmentService) {}

    public function show(Request $request, int $lessonId): JsonResponse
    {
        $lesson = Lesson::with(['module', 'course'])->find($lessonId);

        if (!$lesson) {
            return $this->error('Lesson not found', 404);
        }

        $user = $request->user();

        // Free preview: accessible without enrollment
        if (!$lesson->is_free_preview) {
            $enrollment = Enrollment::where('user_id', $user->id)
                ->where('course_id', $lesson->course_id)
                ->where('payment_status', 'paid')
                ->first();

            if (!$enrollment || !$enrollment->hasActiveAccess()) {
                return $this->error('You must be enrolled to access this lesson', 403);
            }
        }

        $progress = StudentProgress::where('user_id', $user->id)
            ->where('lesson_id', $lessonId)
            ->first();

        $isBookmarked = Bookmark::where('user_id', $user->id)
            ->where('lesson_id', $lessonId)
            ->exists();

        $note = Note::where('user_id', $user->id)
            ->where('lesson_id', $lessonId)
            ->first();

        return $this->success([
            'id'               => $lesson->id,
            'title'            => $lesson->title,
            'type'             => $lesson->type,
            'content'          => $lesson->content,
            'video_url'        => $lesson->video_url,
            'video_provider'   => $lesson->video_provider,
            'duration_minutes' => $lesson->duration_minutes,
            'is_free_preview'  => (bool) $lesson->is_free_preview,
            'attachment_url'   => $lesson->attachment ? asset('storage/' . $lesson->attachment) : null,
            'module'           => $lesson->module ? ['id' => $lesson->module->id, 'title' => $lesson->module->title] : null,
            'progress'         => $progress ? [
                'is_completed'       => (bool) $progress->is_completed,
                'watch_time_seconds' => $progress->watch_time_seconds,
                'completed_at'       => $progress->completed_at?->toDateTimeString(),
            ] : null,
            'is_bookmarked'    => $isBookmarked,
            'note'             => $note ? ['id' => $note->id, 'content' => $note->content] : null,
        ]);
    }

    public function complete(Request $request, int $lessonId): JsonResponse
    {
        $request->validate(['watch_time_seconds' => 'nullable|integer|min:0']);

        $lesson = Lesson::find($lessonId);
        if (!$lesson) {
            return $this->error('Lesson not found', 404);
        }

        $user = $request->user();

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $lesson->course_id)
            ->where('payment_status', 'paid')
            ->first();

        if (!$enrollment || !$enrollment->hasActiveAccess()) {
            return $this->error('You must be enrolled to mark progress', 403);
        }

        $progress = StudentProgress::updateOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lessonId, 'course_id' => $lesson->course_id],
            [
                'is_completed'       => true,
                'watch_time_seconds' => $request->watch_time_seconds ?? 0,
                'completed_at'       => now(),
                'last_watched_at'    => now(),
            ]
        );

        $this->enrollmentService->updateProgress($user, $lesson->course_id);

        $enrollment->refresh();

        return $this->success([
            'lesson_completed'    => true,
            'course_progress'     => (float) $enrollment->progress_percent,
            'course_completed'    => $enrollment->status === 'completed',
        ], message: 'Lesson marked as complete');
    }

    public function updateProgress(Request $request, int $lessonId): JsonResponse
    {
        $request->validate(['watch_time_seconds' => 'required|integer|min:0']);

        $lesson = Lesson::find($lessonId);
        if (!$lesson) {
            return $this->error('Lesson not found', 404);
        }

        $user = $request->user();

        StudentProgress::updateOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lessonId, 'course_id' => $lesson->course_id],
            [
                'watch_time_seconds' => $request->watch_time_seconds,
                'last_watched_at'    => now(),
            ]
        );

        return $this->success([], message: 'Progress updated');
    }

    public function toggleBookmark(Request $request, int $lessonId): JsonResponse
    {
        $lesson = Lesson::find($lessonId);
        if (!$lesson) {
            return $this->error('Lesson not found', 404);
        }

        $user = $request->user();

        $bookmark = Bookmark::where('user_id', $user->id)->where('lesson_id', $lessonId)->first();

        if ($bookmark) {
            $bookmark->delete();
            return $this->success(['bookmarked' => false], message: 'Bookmark removed');
        }

        Bookmark::create([
            'user_id'   => $user->id,
            'lesson_id' => $lessonId,
            'course_id' => $lesson->course_id,
        ]);

        return $this->success(['bookmarked' => true], message: 'Lesson bookmarked');
    }

    public function getBookmarks(Request $request): JsonResponse
    {
        $bookmarks = Bookmark::where('user_id', $request->user()->id)
            ->with(['lesson.course'])
            ->latest()
            ->get();

        return $this->success(
            $bookmarks->map(fn($b) => [
                'id'         => $b->id,
                'lesson_id'  => $b->lesson_id,
                'lesson'     => $b->lesson ? [
                    'id'    => $b->lesson->id,
                    'title' => $b->lesson->title,
                    'type'  => $b->lesson->type,
                    'course'=> $b->lesson->course ? [
                        'id'    => $b->lesson->course->id,
                        'title' => $b->lesson->course->title,
                        'slug'  => $b->lesson->course->slug,
                    ] : null,
                ] : null,
                'created_at' => $b->created_at?->toDateTimeString(),
            ])->values()
        );
    }

    public function saveNote(Request $request, int $lessonId): JsonResponse
    {
        $request->validate(['content' => 'required|string|max:5000']);

        $lesson = Lesson::find($lessonId);
        if (!$lesson) {
            return $this->error('Lesson not found', 404);
        }

        $user = $request->user();

        $note = Note::updateOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lessonId],
            ['content' => $request->content, 'course_id' => $lesson->course_id]
        );

        return $this->success(['id' => $note->id, 'content' => $note->content], message: 'Note saved');
    }

    public function deleteNote(Request $request, int $lessonId): JsonResponse
    {
        Note::where('user_id', $request->user()->id)
            ->where('lesson_id', $lessonId)
            ->delete();

        return $this->success([], message: 'Note deleted');
    }

    public function getNotes(Request $request): JsonResponse
    {
        $notes = Note::where('user_id', $request->user()->id)
            ->with('lesson:id,title,course_id')
            ->latest()
            ->get();

        return $this->success(
            $notes->map(fn($n) => [
                'id'        => $n->id,
                'content'   => $n->content,
                'lesson_id' => $n->lesson_id,
                'course_id' => $n->course_id,
                'lesson'    => $n->lesson ? ['id' => $n->lesson->id, 'title' => $n->lesson->title] : null,
                'updated_at'=> $n->updated_at?->toDateTimeString(),
            ])->values()
        );
    }

    public function navigation(Request $request, int $lessonId): JsonResponse
    {
        $lesson = Lesson::find($lessonId);
        if (!$lesson) {
            return $this->error('Lesson not found', 404);
        }

        $user = $request->user();

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $lesson->course_id)
            ->where('payment_status', 'paid')
            ->first();

        if (!$enrollment) {
            return $this->error('Not enrolled', 403);
        }

        $allLessons = Lesson::where('course_id', $lesson->course_id)
            ->where('is_published', true)
            ->with('module:id,sort_order')
            ->get()
            ->sortBy(fn($l) => [$l->module?->sort_order ?? 0, $l->sort_order ?? 0])
            ->values();

        $index = $allLessons->search(fn($l) => $l->id === $lessonId);

        $prev = ($index !== false && $index > 0) ? $allLessons[$index - 1] : null;
        $next = ($index !== false && $index < $allLessons->count() - 1) ? $allLessons[$index + 1] : null;

        return $this->success([
            'prev' => $prev ? ['id' => $prev->id, 'title' => $prev->title] : null,
            'next' => $next ? ['id' => $next->id, 'title' => $next->title] : null,
        ]);
    }
}
