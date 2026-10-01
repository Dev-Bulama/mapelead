<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Instructor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InstructorApiController extends Controller
{
    use ApiResponseTrait;

    private function getInstructor(Request $request): ?Instructor
    {
        return Instructor::where('user_id', $request->user()->id)->first();
    }

    public function dashboard(Request $request): JsonResponse
    {
        $instructor = $this->getInstructor($request);

        if (!$instructor) {
            return $this->error('Instructor profile not found.', 404);
        }

        $courseIds = Course::where('instructor_id', $instructor->id)->pluck('id');

        $courses = Course::where('instructor_id', $instructor->id)
            ->withCount('enrollments')
            ->orderByDesc('created_at')
            ->get();

        $totalStudents  = $courses->sum('enrollments_count');
        $publishedCount = $courses->where('status', 'published')->count();

        $recentEnrollments = Enrollment::whereIn('course_id', $courseIds)
            ->with([
                'user:id,full_name,email,avatar_url',
                'course:id,title,slug',
            ])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $pendingSubmissions = AssignmentSubmission::whereHas('assignment.course', function ($q) use ($instructor) {
            $q->where('instructor_id', $instructor->id);
        })
            ->where('status', 'submitted')
            ->with([
                'assignment:id,title',
                'user:id,full_name',
            ])
            ->orderByDesc('submitted_at')
            ->limit(10)
            ->get();

        return $this->success([
            'stats' => [
                'total_courses'   => $courses->count(),
                'published'       => $publishedCount,
                'total_students'  => $totalStudents,
                'pending_reviews' => $pendingSubmissions->count(),
            ],
            'courses'             => $courses,
            'recent_enrollments'  => $recentEnrollments,
            'pending_submissions' => $pendingSubmissions,
        ]);
    }

    public function courses(Request $request): JsonResponse
    {
        $instructor = $this->getInstructor($request);

        if (!$instructor) {
            return $this->error('Instructor profile not found.', 404);
        }

        $courses = Course::where('instructor_id', $instructor->id)
            ->withCount(['enrollments', 'lessons'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return $this->success($courses);
    }

    public function submissions(Request $request): JsonResponse
    {
        $instructor = $this->getInstructor($request);

        if (!$instructor) {
            return $this->error('Instructor profile not found.', 404);
        }

        $submissions = AssignmentSubmission::whereHas('assignment.course', function ($q) use ($instructor) {
            $q->where('instructor_id', $instructor->id);
        })
            ->with([
                'assignment:id,title,course_id',
                'user:id,full_name,email,avatar_url',
            ])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderByDesc('submitted_at')
            ->paginate(20);

        return $this->success($submissions);
    }
}
