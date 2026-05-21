<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StudentProgress extends Model
{
    protected $fillable = [
        'user_id', 'course_id', 'lesson_id',
        'is_completed', 'watch_time_seconds', 'last_watched_at', 'completed_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'last_watched_at' => 'datetime', 'completed_at' => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function course() { return $this->belongsTo(Course::class); }
    public function lesson() { return $this->belongsTo(Lesson::class); }
}
