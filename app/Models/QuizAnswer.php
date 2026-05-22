<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAnswer extends Model
{
    protected $fillable = ['attempt_id', 'question_id', 'selected_option_ids', 'text_answer', 'is_correct', 'points_earned'];
    protected $casts = ['selected_option_ids' => 'array', 'is_correct' => 'boolean'];
    public function attempt() { return $this->belongsTo(QuizAttempt::class); }
    public function question() { return $this->belongsTo(QuizQuestion::class); }
}
