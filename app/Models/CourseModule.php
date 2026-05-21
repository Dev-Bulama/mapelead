<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CourseModule extends Model
{
    protected $fillable = ['course_id', 'title', 'description', 'sort_order', 'is_free_preview'];
    protected $casts = ['is_free_preview' => 'boolean'];

    public function course() { return $this->belongsTo(Course::class); }
    public function lessons() { return $this->hasMany(Lesson::class, 'module_id')->orderBy('sort_order'); }
}
