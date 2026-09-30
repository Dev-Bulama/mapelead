<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Course;
use App\Models\Enrollment;
use App\Services\Payment\PaymentService;
use App\Services\Student\EnrollmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EnrollmentApiController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private readonly EnrollmentService $enrollmentService,
        private readonly PaymentService $paymentService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $enrollments = $this->enrollmentService->getStudentEnrollments($request->user());

        return $this->success(
            $enrollments->map(fn($e) => $this->formatEnrollment($e))->values()
        );
    }

    public function show(Request $request, int $enrollmentId): JsonResponse
    {
        $enrollment = Enrollment::where('id', $enrollmentId)
            ->where('user_id', $request->user()->id)
            ->with(['course.modules.lessons', 'course.category'])
            ->first();

        if (!$enrollment) {
            return $this->error('Enrollment not found', 404);
        }

        return $this->success($this->formatEnrollment($enrollment, detailed: true));
    }

    public function enroll(Request $request, int $courseId): JsonResponse
    {
        $request->validate([
            'training_type' => 'nullable|in:online,physical_monthly,physical_quarterly',
            'payment_type'  => 'nullable|in:full,installment',
            'installments'  => 'nullable|integer|min:2|max:4',
        ]);

        $course = Course::published()->find($courseId);
        if (!$course) {
            return $this->error('Course not found', 404);
        }

        $user = $request->user();

        $existing = $this->enrollmentService->checkEnrollment($user, $course);
        if ($existing && in_array($existing->status, ['active', 'completed'])) {
            return $this->error('You are already enrolled in this course', 409);
        }

        $trainingType = $request->training_type ?? 'online';
        $price        = $course->getPriceForMode($trainingType);

        $enrollment = $this->enrollmentService->enroll($user, $course, [
            'training_type' => $trainingType,
            'payment_type'  => $request->payment_type ?? 'full',
            'amount'        => $price,
        ]);

        // Free course — already activated by the service
        if ($price == 0 || $enrollment->payment_status === 'paid') {
            return $this->success([
                'enrollment' => $this->formatEnrollment($enrollment),
                'is_free'    => true,
            ], 201, 'Enrolled successfully');
        }

        // Paid course — initiate payment
        try {
            $payment = $this->paymentService->initiate($enrollment, 'paystack', [
                'installments' => $request->installments,
            ]);

            return $this->success([
                'enrollment'       => $this->formatEnrollment($enrollment),
                'payment_url'      => $payment['authorization_url'] ?? null,
                'payment_reference'=> $payment['reference'] ?? null,
                'is_free'          => false,
            ], 201, 'Enrollment initiated — complete payment to activate');
        } catch (\Throwable $e) {
            Log::error('Enrollment payment initiation failed', ['error' => $e->getMessage()]);
            return $this->error('Could not initiate payment. Please try again.', 500);
        }
    }

    public function paymentVerify(Request $request): JsonResponse
    {
        $request->validate(['reference' => 'required|string']);

        try {
            $enrollment = $this->paymentService->verify($request->reference, 'paystack');
            return $this->success([
                'enrollment' => $this->formatEnrollment($enrollment),
            ], message: 'Payment verified successfully');
        } catch (\Throwable $e) {
            Log::error('Payment verification failed', ['error' => $e->getMessage()]);
            return $this->error('Payment verification failed: ' . $e->getMessage(), 422);
        }
    }

    private function formatEnrollment(Enrollment $enrollment, bool $detailed = false): array
    {
        $data = [
            'id'              => $enrollment->id,
            'status'          => $enrollment->status,
            'payment_status'  => $enrollment->payment_status,
            'payment_type'    => $enrollment->payment_type,
            'training_type'   => $enrollment->training_type,
            'training_label'  => $enrollment->training_type_label,
            'amount_paid'     => (float) ($enrollment->amount_paid ?? 0),
            'progress_percent'=> (float) ($enrollment->progress_percent ?? 0),
            'enrolled_at'     => $enrollment->enrolled_at?->toDateTimeString(),
            'completed_at'    => $enrollment->completed_at?->toDateTimeString(),
            'expires_at'      => $enrollment->expires_at?->toDateTimeString(),
            'has_access'      => $enrollment->hasActiveAccess(),
            'course'          => $enrollment->course ? [
                'id'            => $enrollment->course->id,
                'title'         => $enrollment->course->title,
                'slug'          => $enrollment->course->slug,
                'thumbnail_url' => $enrollment->course->thumbnail_url,
                'level'         => $enrollment->course->level,
                'category'      => $enrollment->course->category?->name,
            ] : null,
        ];

        if ($detailed && $enrollment->relationLoaded('course')) {
            $course = $enrollment->course;
            $data['modules'] = $course->modules ? $course->modules->map(fn($m) => [
                'id'      => $m->id,
                'title'   => $m->title,
                'lessons' => $m->lessons ? $m->lessons->map(fn($l) => [
                    'id'               => $l->id,
                    'title'            => $l->title,
                    'type'             => $l->type,
                    'duration_minutes' => $l->duration_minutes,
                ])->values() : [],
            ])->values() : [];
        }

        return $data;
    }
}
