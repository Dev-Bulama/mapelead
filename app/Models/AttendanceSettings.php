<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSettings extends Model
{
    protected $table = 'attendance_settings';

    protected $fillable = [
        'course_id', 'minimum_percentage', 'notify_threshold',
        'notify_student_below_threshold', 'notify_admin_below_threshold',
    ];

    protected $casts = [
        'notify_student_below_threshold' => 'boolean',
        'notify_admin_below_threshold'   => 'boolean',
    ];

    public function course() { return $this->belongsTo(Course::class); }
}
