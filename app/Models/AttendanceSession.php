<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSession extends Model
{
    protected $fillable = [
        'course_id', 'batch_id', 'title', 'date',
        'start_time', 'end_time', 'type', 'description', 'created_by',
    ];

    protected $casts = ['date' => 'date'];

    public function course() { return $this->belongsTo(Course::class); }
    public function batch() { return $this->belongsTo(Batch::class); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }
    public function records() { return $this->hasMany(AttendanceRecord::class, 'session_id'); }

    public function recordFor(int $userId) {
        return $this->records()->where('user_id', $userId)->first();
    }

    public function presentCount(): int {
        return $this->records()->whereIn('status', ['present', 'late'])->count();
    }

    public function absentCount(): int {
        return $this->records()->where('status', 'absent')->count();
    }
}
