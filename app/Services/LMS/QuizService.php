<?php
namespace App\Services\LMS;

use App\Models\Enrollment;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use Illuminate\Support\Facades\DB;

class QuizService
{
    public function startAttempt(Quiz $quiz, int $userId, int $enrollmentId): QuizAttempt
    {
        $attemptNumber = QuizAttempt::where('user_id', $userId)
            ->where('quiz_id', $quiz->id)->count() + 1;

        if ($attemptNumber > $quiz->max_attempts) {
            throw new \Exception('Maximum attempts reached for this quiz.');
        }

        return QuizAttempt::create([
            'user_id'        => $userId,
            'quiz_id'        => $quiz->id,
            'enrollment_id'  => $enrollmentId,
            'attempt_number' => $attemptNumber,
            'started_at'     => now(),
        ]);
    }

    public function submitAttempt(QuizAttempt $attempt, array $answers): QuizAttempt
    {
        DB::transaction(function () use ($attempt, $answers) {
            $quiz = $attempt->quiz->load('questions.options');
            $totalPoints = 0;
            $earnedPoints = 0;

            foreach ($quiz->questions as $question) {
                $totalPoints += $question->points;
                $userAnswer = $answers[$question->id] ?? null;

                $isCorrect = false;
                $pointsEarned = 0;

                if ($question->type === 'short_answer') {
                    $correctOptions = $question->correctOptions->pluck('option_text')->map(fn($t) => strtolower(trim($t)));
                    $isCorrect = in_array(strtolower(trim($userAnswer ?? '')), $correctOptions->toArray());
                } elseif ($question->type === 'multiple_choice') {
                    $selectedIds = is_array($userAnswer) ? $userAnswer : [];
                    $correctIds = $question->correctOptions->pluck('id')->sort()->values()->toArray();
                    sort($selectedIds);
                    $isCorrect = $selectedIds === $correctIds;
                } else {
                    $correctId = $question->correctOptions->first()?->id;
                    $isCorrect = $userAnswer == $correctId;
                }

                if ($isCorrect) {
                    $pointsEarned = $question->points;
                    $earnedPoints += $pointsEarned;
                }

                QuizAnswer::create([
                    'attempt_id'          => $attempt->id,
                    'question_id'         => $question->id,
                    'selected_option_ids' => is_array($userAnswer) ? $userAnswer : ($userAnswer ? [$userAnswer] : null),
                    'text_answer'         => is_string($userAnswer) && $question->type === 'short_answer' ? $userAnswer : null,
                    'is_correct'          => $isCorrect,
                    'points_earned'       => $pointsEarned,
                ]);
            }

            $scorePercent = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100, 2) : 0;
            $passed = $scorePercent >= $attempt->quiz->pass_score;

            $attempt->update([
                'total_points'       => $totalPoints,
                'earned_points'      => $earnedPoints,
                'score_percent'      => $scorePercent,
                'passed'             => $passed,
                'completed_at'       => now(),
                'time_taken_seconds' => now()->diffInSeconds($attempt->started_at),
            ]);
        });

        return $attempt->fresh();
    }
}
