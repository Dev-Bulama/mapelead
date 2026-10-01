<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Course;
use App\Models\CourseReview;
use App\Models\Enrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseReviewApiController extends Controller
{
    use ApiResponseTrait;

    public function index(int $courseId): JsonResponse
    {
        $course = Course::find($courseId);
        if (!$course) {
            return $this->error('Course not found', 404);
        }

        $reviews = CourseReview::where('course_id', $courseId)
            ->where('is_approved', true)
            ->with('user:id,full_name,avatar_url')
            ->orderByDesc('created_at')
            ->paginate(10);

        return $this->success([
            'reviews'      => $reviews->getCollection()->map(fn($r) => $this->formatReview($r))->values(),
            'total'        => $reviews->total(),
            'avg_rating'   => round($reviews->getCollection()->avg('rating') ?? 0, 1),
        ]);
    }

    public function myReview(Request $request, int $courseId): JsonResponse
    {
        $review = CourseReview::where('course_id', $courseId)
            ->where('user_id', $request->user()->id)
            ->first();

        return $this->success($review ? $this->formatReview($review) : null);
    }

    public function store(Request $request, int $courseId): JsonResponse
    {
        $course = Course::find($courseId);
        if (!$course) {
            return $this->error('Course not found', 404);
        }

        $user = $request->user();

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->where('payment_status', 'paid')
            ->first();

        if (!$enrollment) {
            return $this->error('You must be enrolled in this course to leave a review.', 403);
        }

        $existing = CourseReview::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->first();

        if ($existing) {
            return $this->error('You have already submitted a review for this course.', 409);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title'  => 'nullable|string|max:150',
            'body'   => 'nullable|string|max:2000',
        ]);

        $review = CourseReview::create([
            'user_id'       => $user->id,
            'course_id'     => $courseId,
            'enrollment_id' => $enrollment->id,
            'rating'        => $request->rating,
            'title'         => $request->title,
            'body'          => $request->body,
            'is_approved'   => false, // pending admin approval
        ]);

        return $this->success($this->formatReview($review), 201, 'Review submitted. It will appear after approval.');
    }

    public function destroy(Request $request, int $courseId): JsonResponse
    {
        $deleted = CourseReview::where('user_id', $request->user()->id)
            ->where('course_id', $courseId)
            ->delete();

        if (!$deleted) {
            return $this->error('Review not found', 404);
        }

        return $this->success([], message: 'Review deleted');
    }

    private function formatReview(CourseReview $review): array
    {
        return [
            'id'         => $review->id,
            'rating'     => $review->rating,
            'title'      => $review->title,
            'body'       => $review->body,
            'is_approved'=> (bool) $review->is_approved,
            'reviewer'   => $review->user ? [
                'full_name'  => $review->user->full_name,
                'avatar_url' => $review->user->avatar_url,
            ] : null,
            'created_at' => $review->created_at?->toDateString(),
        ];
    }
}
