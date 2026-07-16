<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\StudentProgress;
use App\Services\Payment\InstallmentService;
use App\Services\Payment\PaymentService;
use App\Services\Student\EnrollmentService;
use Illuminate\Http\Request;

class StudentCourseController extends Controller
{
    public function __construct(
        private EnrollmentService $enrollmentService,
        private PaymentService    $paymentService,
        private InstallmentService $installmentService,
    ) {}

    public function index()
    {
        $enrollments = $this->enrollmentService->getStudentEnrollments(auth()->user());
        return view('student.courses', compact('enrollments'));
    }

    public function learn(string $slug)
    {
        $course = Course::published()->where('slug', $slug)->with(['modules.lessons'])->firstOrFail();
        $enrollment = Enrollment::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->whereIn('status', ['active', 'completed'])
            ->firstOrFail();

        // Check access lock
        if ($enrollment->access_locked) {
            return view('student.access-locked', compact('enrollment', 'course'));
        }

        // Payment must be paid or part_paid to access
        if (!in_array($enrollment->payment_status, ['paid', 'part_paid'])) {
            return redirect()->route('enroll.checkout', $slug)
                ->with('error', 'Please complete payment to access this course.');
        }

        $completedLessons = StudentProgress::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->where('is_completed', true)
            ->pluck('lesson_id')
            ->toArray();

        $firstIncomplete = null;
        foreach ($course->modules as $module) {
            foreach ($module->lessons as $lesson) {
                if (!in_array($lesson->id, $completedLessons)) {
                    $firstIncomplete = $lesson;
                    break 2;
                }
            }
        }

        $currentLesson = request('lesson')
            ? $course->modules->flatMap->lessons->firstWhere('id', request('lesson'))
            : ($firstIncomplete ?? $course->modules->first()?->lessons->first());

        return view('student.learn', compact('course', 'enrollment', 'completedLessons', 'currentLesson'));
    }

    public function markComplete(string $slug, int $lessonId, Request $request)
    {
        $course = Course::where('slug', $slug)->firstOrFail();
        Lesson::where('id', $lessonId)->where('course_id', $course->id)->firstOrFail();

        StudentProgress::updateOrCreate(
            ['user_id' => auth()->id(), 'lesson_id' => $lessonId],
            [
                'course_id'          => $course->id,
                'is_completed'       => true,
                'watch_time_seconds' => $request->watch_time ?? 0,
                'last_watched_at'    => now(),
                'completed_at'       => now(),
            ]
        );

        $this->enrollmentService->updateProgress(auth()->user(), $course->id);
        return response()->json(['success' => true, 'message' => 'Lesson marked as complete!']);
    }

    public function checkout(string $slug)
    {
        $course = Course::published()->where('slug', $slug)->with(['instructor.user', 'category'])->firstOrFail();

        $existingEnrollment = $this->enrollmentService->checkEnrollment(auth()->user(), $course);
        if ($existingEnrollment && in_array($existingEnrollment->status, ['active', 'completed'])) {
            return redirect()->route('student.learn', $slug);
        }

        // Load available batches for this course
        $batches = Batch::where('course_id', $course->id)
            ->where('status', 'active')
            ->get();

        $prices = [
            'online'              => $course->getPriceForMode('online'),
            'physical_monthly'    => $course->getPriceForMode('physical_monthly'),
            'physical_quarterly'  => $course->getPriceForMode('physical_quarterly'),
        ];
        $installmentOptions = $course->getInstallmentOptionsWithDefaults();

        return view('student.checkout', compact('course', 'batches', 'prices', 'installmentOptions'));
    }

    public function initPayment(string $slug, Request $request)
    {
        $request->validate([
            'payment_type'      => 'required|in:full,installment',
            'training_type'     => 'required|in:online,physical_monthly,physical_quarterly',
            'batch_id'          => 'nullable|exists:batches,id',
            'down_payment'      => 'required_if:payment_type,installment|nullable|numeric|min:1000',
            'installment_count' => 'required_if:payment_type,installment|nullable|integer|min:2|max:10',
            'period_days'       => 'required_if:payment_type,installment|nullable|integer|min:1|max:30',
            'first_due_date'    => 'required_if:payment_type,installment|nullable|date|after:today',
        ]);

        $course = Course::published()->where('slug', $slug)->firstOrFail();
        $modePrice = $course->getPriceForMode($request->training_type);

        if ($course->is_free) {
            $enrollment = $this->enrollmentService->enroll(auth()->user(), $course, [
                'training_type' => $request->training_type,
            ]);
            if ($request->batch_id) {
                \App\Models\BatchEnrollment::firstOrCreate([
                    'batch_id' => $request->batch_id, 'enrollment_id' => $enrollment->id
                ]);
            }
            return redirect()->route('student.learn', $slug)->with('success', 'Enrolled successfully!');
        }

        $enrollment = $this->enrollmentService->enroll(auth()->user(), $course, [
            'payment_type'  => $request->payment_type,
            'training_type' => $request->training_type,
            'amount'        => $modePrice,
        ]);

        if ($request->batch_id) {
            \App\Models\BatchEnrollment::firstOrCreate([
                'batch_id' => $request->batch_id, 'enrollment_id' => $enrollment->id
            ]);
        }

        // For installment, create plan first then pay down payment
        if ($request->payment_type === 'installment') {
            $this->installmentService->createPlan($enrollment, [
                'total_amount'      => $modePrice,
                'down_payment'      => $request->down_payment,
                'installment_count' => $request->installment_count,
                'period_days'       => $request->period_days,
                'first_due_date'    => $request->first_due_date,
                'grace_period_days' => 3,
            ]);

            $paymentAmount = $request->down_payment;
        } else {
            $paymentAmount = $modePrice;
        }

        try {
            $payment = $this->paymentService->initiate($enrollment, 'paystack', [
                'amount'       => $paymentAmount,
                'payment_type' => $request->payment_type,
            ]);

            // If Paystack is configured, redirect to payment page
            if (isset($payment['authorization_url']) && str_contains($payment['authorization_url'], 'paystack.co')) {
                return redirect($payment['authorization_url']);
            }

            // Dev mode: show payment page
            return view('student.payment', compact('course', 'enrollment', 'payment'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Payment initialization failed: ' . $e->getMessage());
        }
    }

    public function paymentCallback(string $reference)
    {
        try {
            $this->paymentService->verify($reference, 'paystack');
            return redirect()->route('student.dashboard')
                ->with('success', 'Payment successful! You are now enrolled. Check your dashboard for your admission number.');
        } catch (\Exception $e) {
            return redirect()->route('courses.index')
                ->with('error', 'Payment verification failed. Contact support with reference: ' . $reference);
        }
    }

    public function paystackWebhook(Request $request)
    {
        $signature = $request->header('x-paystack-signature');
        $payload   = $request->getContent();

        $paystack = app(\App\Services\Payment\PaystackService::class);

        if (!$paystack->verifyWebhookSignature($payload, $signature ?? '')) {
            return response('Unauthorized', 401);
        }

        $this->paymentService->handleWebhook($request->all());
        return response('OK', 200);
    }
}
