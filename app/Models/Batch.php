<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Batch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'course_id', 'name', 'code', 'description',
        'start_date', 'end_date', 'max_students', 'current_students', 'status',
    ];

    protected $casts = ['start_date' => 'date', 'end_date' => 'date'];

    public function course() { return $this->belongsTo(Course::class); }
    public function batchEnrollments() { return $this->hasMany(BatchEnrollment::class); }
    public function enrollments() { return $this->belongsToMany(Enrollment::class, 'batch_enrollments'); }
    public function attendanceSessions() { return $this->hasMany(AttendanceSession::class); }

    public function isActive(): bool { return $this->status === 'active'; }
    public function hasCapacity(): bool { return is_null($this->max_students) || $this->current_students < $this->max_students; }
}
