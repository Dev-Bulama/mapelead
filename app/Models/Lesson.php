<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'module_id', 'course_id', 'title', 'type', 'content',
        'video_url', 'video_provider', 'duration_minutes', 'attachment',
        'sort_order', 'is_free_preview', 'is_published',
    ];
    protected $casts = ['is_free_preview' => 'boolean', 'is_published' => 'boolean'];

    public function module() { return $this->belongsTo(CourseModule::class); }
    public function course() { return $this->belongsTo(Course::class); }
    public function progress() { return $this->hasMany(StudentProgress::class); }
}
