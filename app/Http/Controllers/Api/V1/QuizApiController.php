<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Enrollment;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuizApiController extends Controller
{
    use ApiResponseTrait;

    public function show(Request $request, int $quizId): JsonResponse
    {
        $quiz = Quiz::with(['questions.options'])->find($quizId);

        if (!$quiz || !$quiz->is_active) {
            return $this->error('Quiz not found', 404);
        }

        $user = $request->user();

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $quiz->course_id)
            ->where('payment_status', 'paid')
            ->first();

        if (!$enrollment || !$enrollment->hasActiveAccess()) {
            return $this->error('You must be enrolled to access this quiz', 403);
        }

        $attemptCount  = $quiz->attemptsFor($user->id)->count();
        $hasPassedQuiz = $quiz->userPassed($user->id);

        if ($quiz->max_attempts > 0 && $attemptCount >= $quiz->max_attempts && !$hasPassedQuiz) {
            return $this->error('You have reached the maximum number of attempts for this quiz', 403);
        }

        $questions = $quiz->questions;
        if ($quiz->shuffle_questions) {
            $questions = $questions->shuffle();
        }

        return $this->success([
            'id'                 => $quiz->id,
            'title'              => $quiz->title,
            'description'        => $quiz->description,
            'pass_score'         => $quiz->pass_score,
            'time_limit_minutes' => $quiz->time_limit_minutes,
            'max_attempts'       => $quiz->max_attempts,
            'attempts_taken'     => $attemptCount,
            'has_passed'         => $hasPassedQuiz,
            'questions'          => $questions->map(fn($q) => [
                'id'          => $q->id,
                'question'    => $q->question,
                'type'        => $q->type ?? 'single',
                'points'      => $q->points ?? 1,
                'explanation' => null, // only shown after passing
                'options'     => $q->options->map(fn($o) => [
                    'id'     => $o->id,
                    'option' => $o->option_text,
                ])->values(),
            ])->values(),
        ]);
    }

    public function submit(Request $request, int $quizId): JsonResponse
    {
        $request->validate([
            'answers'                 => 'required|array',
            'answers.*.question_id'   => 'required|integer',
            'answers.*.option_ids'    => 'required|array|min:1',
            'answers.*.option_ids.*'  => 'integer',
            'time_taken_seconds'      => 'nullable|integer|min:0',
        ]);

        $quiz = Quiz::with(['questions.options'])->find($quizId);
        if (!$quiz || !$quiz->is_active) {
            return $this->error('Quiz not found', 404);
        }

        $user = $request->user();

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $quiz->course_id)
            ->where('payment_status', 'paid')
            ->first();

        if (!$enrollment || !$enrollment->hasActiveAccess()) {
            return $this->error('You must be enrolled to submit this quiz', 403);
        }

        $attemptCount = $quiz->attemptsFor($user->id)->count();
        if ($quiz->max_attempts > 0 && $attemptCount >= $quiz->max_attempts && !$quiz->userPassed($user->id)) {
            return $this->error('You have reached the maximum number of attempts', 403);
        }

        // Score the answers
        $totalPoints  = 0;
        $earnedPoints = 0;
        $answerMap    = collect($request->answers)->keyBy('question_id');

        foreach ($quiz->questions as $question) {
            $points = $question->points ?? 1;
            $totalPoints += $points;

            $submitted = $answerMap->get($question->id);
            if (!$submitted) {
                continue;
            }

            $correctOptionIds = $question->options
                ->where('is_correct', true)
                ->pluck('id')
                ->sort()
                ->values()
                ->toArray();

            $selectedIds = collect($submitted['option_ids'])->sort()->values()->toArray();

            if ($correctOptionIds === $selectedIds) {
                $earnedPoints += $points;
            }
        }

        $scorePercent = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100, 2) : 0;
        $passed       = $scorePercent >= $quiz->pass_score;

        $attempt = QuizAttempt::create([
            'user_id'           => $user->id,
            'quiz_id'           => $quizId,
            'enrollment_id'     => $enrollment->id,
            'attempt_number'    => $attemptCount + 1,
            'score_percent'     => $scorePercent,
            'total_points'      => $totalPoints,
            'earned_points'     => $earnedPoints,
            'passed'            => $passed,
            'started_at'        => now()->subSeconds($request->time_taken_seconds ?? 0),
            'completed_at'      => now(),
            'time_taken_seconds'=> $request->time_taken_seconds ?? 0,
        ]);

        // Store individual answers
        foreach ($request->answers as $answer) {
            $question        = $quiz->questions->firstWhere('id', $answer['question_id']);
            $correctOptionIds = $question?->options->where('is_correct', true)->pluck('id')->toArray() ?? [];
            $selectedIds      = $answer['option_ids'];
            $isCorrect        = !empty($correctOptionIds) &&
                                count(array_diff($correctOptionIds, $selectedIds)) === 0 &&
                                count(array_diff($selectedIds, $correctOptionIds)) === 0;

            QuizAnswer::create([
                'attempt_id'         => $attempt->id,
                'question_id'        => $answer['question_id'],
                'selected_option_ids'=> $selectedIds,
                'is_correct'         => $isCorrect,
                'points_earned'      => $isCorrect ? ($question?->points ?? 1) : 0,
            ]);
        }

        $response = [
            'score_percent'  => $scorePercent,
            'earned_points'  => $earnedPoints,
            'total_points'   => $totalPoints,
            'pass_score'     => $quiz->pass_score,
            'passed'         => $passed,
            'attempt_number' => $attempt->attempt_number,
        ];

        if ($quiz->show_answers_after) {
            $response['correct_answers'] = $quiz->questions->map(fn($q) => [
                'question_id'       => $q->id,
                'correct_option_ids'=> $q->options->where('is_correct', true)->pluck('id')->values(),
                'explanation'       => $q->explanation,
            ])->values();
        }

        return $this->success($response, message: $passed ? 'Congratulations, you passed!' : 'Quiz submitted. Keep studying and try again!');
    }

    public function attempts(Request $request, int $quizId): JsonResponse
    {
        $quiz = Quiz::find($quizId);
        if (!$quiz) {
            return $this->error('Quiz not found', 404);
        }

        $attempts = QuizAttempt::where('user_id', $request->user()->id)
            ->where('quiz_id', $quizId)
            ->orderByDesc('created_at')
            ->get();

        return $this->success(
            $attempts->map(fn($a) => [
                'id'             => $a->id,
                'attempt_number' => $a->attempt_number,
                'score_percent'  => (float) $a->score_percent,
                'passed'         => (bool) $a->passed,
                'completed_at'   => $a->completed_at?->toDateTimeString(),
            ])->values()
        );
    }
}
