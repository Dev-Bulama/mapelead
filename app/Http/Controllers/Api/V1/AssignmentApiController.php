<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Enrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssignmentApiController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request, int $courseId): JsonResponse
    {
        $user = $request->user();

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->where('payment_status', 'paid')
            ->first();

        if (!$enrollment || !$enrollment->hasActiveAccess()) {
            return $this->error('You must be enrolled to view assignments', 403);
        }

        $assignments = Assignment::where('course_id', $courseId)
            ->where('is_active', true)
            ->get();

        return $this->success(
            $assignments->map(fn($a) => $this->formatAssignment($a, $user->id))->values()
        );
    }

    public function show(Request $request, int $assignmentId): JsonResponse
    {
        $assignment = Assignment::find($assignmentId);
        if (!$assignment || !$assignment->is_active) {
            return $this->error('Assignment not found', 404);
        }

        $user = $request->user();

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $assignment->course_id)
            ->where('payment_status', 'paid')
            ->first();

        if (!$enrollment || !$enrollment->hasActiveAccess()) {
            return $this->error('You must be enrolled to view this assignment', 403);
        }

        return $this->success($this->formatAssignment($assignment, $user->id, detailed: true));
    }

    public function submit(Request $request, int $assignmentId): JsonResponse
    {
        $assignment = Assignment::find($assignmentId);
        if (!$assignment || !$assignment->is_active) {
            return $this->error('Assignment not found', 404);
        }

        $user = $request->user();

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $assignment->course_id)
            ->where('payment_status', 'paid')
            ->first();

        if (!$enrollment || !$enrollment->hasActiveAccess()) {
            return $this->error('You must be enrolled to submit this assignment', 403);
        }

        $existing = $assignment->submissionFor($user->id);
        if ($existing && in_array($existing->status, ['submitted', 'graded'])) {
            return $this->error('You have already submitted this assignment', 409);
        }

        $allowedTypes = $assignment->getAllowedTypesArray();
        $rules = [
            'notes' => 'nullable|string|max:2000',
        ];

        if ($allowedTypes) {
            $rules['file'] = [
                'nullable',
                'file',
                'max:' . (($assignment->max_file_size_mb ?? 10) * 1024),
                'mimes:' . implode(',', $allowedTypes),
            ];
        } else {
            $rules['file'] = 'nullable|file|max:10240';
        }

        $request->validate($rules);

        $filePath = null;
        $fileName = null;

        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $filePath = $request->file('file')->store(
                'assignments/' . $assignment->course_id . '/' . $user->id,
                'private'
            );
            $fileName = $request->file('file')->getClientOriginalName();
        }

        $submission = AssignmentSubmission::create([
            'user_id'       => $user->id,
            'assignment_id' => $assignmentId,
            'enrollment_id' => $enrollment->id,
            'file_path'     => $filePath,
            'file_name'     => $fileName,
            'notes'         => $request->notes,
            'status'        => 'submitted',
            'submitted_at'  => now(),
        ]);

        return $this->success([
            'id'           => $submission->id,
            'status'       => $submission->status,
            'submitted_at' => $submission->submitted_at?->toDateTimeString(),
        ], 201, 'Assignment submitted successfully');
    }

    private function formatAssignment(Assignment $assignment, int $userId, bool $detailed = false): array
    {
        $submission = $assignment->submissionFor($userId);

        $data = [
            'id'            => $assignment->id,
            'title'         => $assignment->title,
            'description'   => $assignment->description,
            'max_score'     => $assignment->max_score,
            'pass_score'    => $assignment->pass_score,
            'is_required'   => (bool) $assignment->is_required,
            'allowed_types' => $assignment->getAllowedTypesArray(),
            'max_file_mb'   => $assignment->max_file_size_mb,
            'submission'    => $submission ? [
                'id'          => $submission->id,
                'status'      => $submission->status,
                'score'       => $submission->score,
                'feedback'    => $submission->feedback,
                'passed'      => $submission->passed(),
                'submitted_at'=> $submission->submitted_at?->toDateTimeString(),
                'graded_at'   => $submission->graded_at?->toDateTimeString(),
            ] : null,
        ];

        if ($detailed) {
            $data['instructions'] = $assignment->instructions;
        }

        return $data;
    }
}
