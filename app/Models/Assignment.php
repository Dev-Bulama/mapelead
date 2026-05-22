<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = [
        'course_id', 'lesson_id', 'title', 'description', 'instructions',
        'max_score', 'pass_score', 'due_days_after_enrollment',
        'allowed_file_types', 'max_file_size_mb', 'is_required', 'is_active',
    ];

    protected $casts = ['is_required' => 'boolean', 'is_active' => 'boolean'];

    public function course() { return $this->belongsTo(Course::class); }
    public function lesson() { return $this->belongsTo(Lesson::class); }
    public function submissions() { return $this->hasMany(AssignmentSubmission::class); }

    public function submissionFor(int $userId) {
        return $this->submissions()->where('user_id', $userId)->first();
    }

    public function getAllowedTypesArray(): array {
        return array_map('trim', explode(',', $this->allowed_file_types));
    }
}
