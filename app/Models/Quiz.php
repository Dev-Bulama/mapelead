<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = [
        'course_id', 'lesson_id', 'title', 'description',
        'pass_score', 'time_limit_minutes', 'max_attempts',
        'shuffle_questions', 'show_answers_after', 'is_required', 'is_active',
    ];

    protected $casts = [
        'shuffle_questions' => 'boolean',
        'show_answers_after' => 'boolean',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function course() { return $this->belongsTo(Course::class); }
    public function lesson() { return $this->belongsTo(Lesson::class); }
    public function questions() { return $this->hasMany(QuizQuestion::class)->orderBy('sort_order'); }
    public function attempts() { return $this->hasMany(QuizAttempt::class); }

    public function attemptsFor(int $userId) {
        return $this->attempts()->where('user_id', $userId);
    }

    public function userPassed(int $userId): bool {
        return $this->attempts()->where('user_id', $userId)->where('passed', true)->exists();
    }
}
