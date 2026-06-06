<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseInstructor extends Model
{
    protected $fillable = ['course_id', 'instructor_id', 'session', 'session_time', 'sort_order'];

    public function course()      { return $this->belongsTo(Course::class); }
    public function instructor()  { return $this->belongsTo(Instructor::class); }

    public function getSessionLabelAttribute(): string
    {
        return match($this->session) {
            'morning'   => 'Morning',
            'afternoon' => 'Afternoon',
            'evening'   => 'Evening',
            default     => ucfirst($this->session),
        };
    }

    public function getFormattedTimeAttribute(): string
    {
        if (!$this->session_time) return '—';
        return date('g:i A', strtotime($this->session_time));
    }

    public function getSessionIconAttribute(): string
    {
        return match($this->session) {
            'morning'   => '🌅',
            'afternoon' => '☀️',
            'evening'   => '🌙',
            default     => '⏰',
        };
    }
}
