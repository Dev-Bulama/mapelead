<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignmentSubmission extends Model
{
    protected $fillable = [
        'user_id', 'assignment_id', 'enrollment_id', 'file_path', 'file_name',
        'notes', 'score', 'status', 'feedback', 'graded_by', 'graded_at', 'submitted_at',
    ];

    protected $casts = ['graded_at' => 'datetime', 'submitted_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }
    public function assignment() { return $this->belongsTo(Assignment::class); }
    public function enrollment() { return $this->belongsTo(Enrollment::class); }
    public function gradedBy() { return $this->belongsTo(User::class, 'graded_by'); }

    public function passed(): bool {
        return $this->score !== null && $this->score >= $this->assignment->pass_score;
    }
}
