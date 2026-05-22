<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Services\LMS\QuizService;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function show(Quiz $quiz)
    {
        $quiz->load('questions.options');
        $enrollment = Enrollment::where('user_id', auth()->id())
            ->where('course_id', $quiz->course_id)
            ->where('status', 'active')
            ->first();

        $attempts = QuizAttempt::where('user_id', auth()->id())
            ->where('quiz_id', $quiz->id)
            ->orderByDesc('created_at')
            ->get();

        return view('student.quiz.show', compact('quiz', 'enrollment', 'attempts'));
    }

    public function start(Request $request, Quiz $quiz)
    {
        $enrollment = Enrollment::where('user_id', auth()->id())
            ->where('course_id', $quiz->course_id)
            ->where('status', 'active')
            ->firstOrFail();

        try {
            $attempt = (new QuizService())->startAttempt($quiz, auth()->id(), $enrollment->id);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        $quiz->load('questions.options');
        return view('student.quiz.take', compact('quiz', 'attempt'));
    }

    public function submit(Request $request, QuizAttempt $attempt)
    {
        abort_if($attempt->user_id !== auth()->id(), 403);

        if ($attempt->completed_at) {
            return redirect()->route('student.quiz.result', $attempt);
        }

        $attempt = (new QuizService())->submitAttempt($attempt, $request->answers ?? []);
        return redirect()->route('student.quiz.result', $attempt);
    }

    public function result(QuizAttempt $attempt)
    {
        abort_if($attempt->user_id !== auth()->id(), 403);
        $attempt->load('quiz.questions.options', 'answers');
        return view('student.quiz.result', compact('attempt'));
    }
}
