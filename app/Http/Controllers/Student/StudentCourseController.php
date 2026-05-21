<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\StudentProgress;
use App\Services\Student\EnrollmentService;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;

class StudentCourseController extends Controller
{
    public function __construct(
        private EnrollmentService $enrollmentService,
        private PaymentService $paymentService
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
            ->where('payment_status', 'paid')
            ->firstOrFail();

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
        $lesson = Lesson::where('id', $lessonId)->where('course_id', $course->id)->firstOrFail();

        StudentProgress::updateOrCreate(
            ['user_id' => auth()->id(), 'lesson_id' => $lessonId],
            [
                'course_id'         => $course->id,
                'is_completed'      => true,
                'watch_time_seconds'=> $request->watch_time ?? 0,
                'last_watched_at'   => now(),
                'completed_at'      => now(),
            ]
        );

        $this->enrollmentService->updateProgress(auth()->user(), $course->id);

        return response()->json(['success' => true, 'message' => 'Lesson marked as complete!']);
    }

    public function checkout(string $slug)
    {
        $course = Course::published()->where('slug', $slug)->with('instructor.user')->firstOrFail();
        $existingEnrollment = $this->enrollmentService->checkEnrollment(auth()->user(), $course);
        if ($existingEnrollment) return redirect()->route('student.learn', $slug);
        return view('student.checkout', compact('course'));
    }

    public function initPayment(string $slug, Request $request)
    {
        $course = Course::published()->where('slug', $slug)->firstOrFail();

        if ($course->is_free) {
            $enrollment = $this->enrollmentService->enroll(auth()->user(), $course);
            return redirect()->route('student.learn', $slug)->with('success', 'Enrolled successfully!');
        }

        $enrollment = $this->enrollmentService->enroll(auth()->user(), $course, [
            'amount' => $course->effective_price,
        ]);

        $payment = $this->paymentService->initiate($enrollment, $request->gateway ?? 'paystack');

        return view('student.payment', compact('course', 'enrollment', 'payment'));
    }

    public function paymentCallback(string $reference)
    {
        try {
            $this->paymentService->verify($reference, 'paystack');
            return redirect()->route('student.dashboard')->with('success', 'Payment successful! You are now enrolled.');
        } catch (\Exception $e) {
            return redirect()->route('student.dashboard')->withErrors(['payment' => 'Payment verification failed.']);
        }
    }
}
