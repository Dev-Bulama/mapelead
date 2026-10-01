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

    public function showSubmission(Request $request, int $submissionId): JsonResponse
    {
        $instructor = $this->getInstructor($request);
        if (!$instructor) {
            return $this->error('Instructor profile not found.', 404);
        }

        $submission = AssignmentSubmission::whereHas('assignment.course', function ($q) use ($instructor) {
            $q->where('instructor_id', $instructor->id);
        })
            ->with(['assignment', 'user:id,full_name,email,avatar_url'])
            ->find($submissionId);

        if (!$submission) {
            return $this->error('Submission not found', 404);
        }

        return $this->success([
            'id'           => $submission->id,
            'status'       => $submission->status,
            'notes'        => $submission->notes,
            'file_name'    => $submission->file_name,
            'has_file'     => !empty($submission->file_path),
            'file_url'     => $submission->file_path ? asset('storage/' . $submission->file_path) : null,
            'score'        => $submission->score,
            'feedback'     => $submission->feedback,
            'passed'       => $submission->score !== null ? $submission->passed() : null,
            'submitted_at' => $submission->submitted_at?->toDateTimeString(),
            'graded_at'    => $submission->graded_at?->toDateTimeString(),
            'user'         => [
                'id'         => $submission->user->id,
                'full_name'  => $submission->user->full_name,
                'email'      => $submission->user->email,
                'avatar_url' => $submission->user->avatar_url,
            ],
            'assignment'   => $submission->assignment ? [
                'id'          => $submission->assignment->id,
                'title'       => $submission->assignment->title,
                'description' => $submission->assignment->description,
                'max_score'   => $submission->assignment->max_score,
                'pass_score'  => $submission->assignment->pass_score,
            ] : null,
        ]);
    }

    public function gradeSubmission(Request $request, int $submissionId): JsonResponse
    {
        $instructor = $this->getInstructor($request);
        if (!$instructor) {
            return $this->error('Instructor profile not found.', 404);
        }

        $submission = AssignmentSubmission::whereHas('assignment.course', function ($q) use ($instructor) {
            $q->where('instructor_id', $instructor->id);
        })->find($submissionId);

        if (!$submission) {
            return $this->error('Submission not found', 404);
        }

        $maxScore = $submission->assignment?->max_score ?? 9999;
        $request->validate([
            'score'    => "required|numeric|min:0|max:{$maxScore}",
            'feedback' => 'nullable|string|max:2000',
        ]);

        $submission->update([
            'score'      => $request->score,
            'feedback'   => $request->feedback,
            'status'     => 'graded',
            'graded_by'  => $request->user()->id,
            'graded_at'  => now(),
        ]);

        return $this->success([
            'id'         => $submission->id,
            'status'     => $submission->status,
            'score'      => $submission->score,
            'feedback'   => $submission->feedback,
            'passed'     => $submission->passed(),
            'graded_at'  => $submission->graded_at?->toDateTimeString(),
        ], message: 'Submission graded successfully');
    }
}
