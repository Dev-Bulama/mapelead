<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    protected $fillable = ['quiz_id', 'question', 'type', 'points', 'sort_order', 'explanation'];

    public function quiz() { return $this->belongsTo(Quiz::class); }
    public function options() { return $this->hasMany(QuizOption::class)->orderBy('sort_order'); }
    public function correctOptions() { return $this->hasMany(QuizOption::class)->where('is_correct', true); }
}
