<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    protected $fillable = [
        'user_id', 'quiz_id', 'enrollment_id', 'attempt_number',
        'score_percent', 'total_points', 'earned_points', 'passed',
        'started_at', 'completed_at', 'time_taken_seconds',
    ];

    protected $casts = [
        'passed' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'score_percent' => 'decimal:2',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function quiz() { return $this->belongsTo(Quiz::class); }
    public function enrollment() { return $this->belongsTo(Enrollment::class); }
    public function answers() { return $this->hasMany(QuizAnswer::class, 'attempt_id'); }
}
