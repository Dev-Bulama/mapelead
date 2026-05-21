<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CourseReview extends Model
{
    protected $fillable = ['user_id', 'course_id', 'enrollment_id', 'rating', 'title', 'body', 'is_approved'];
    protected $casts = ['is_approved' => 'boolean'];

    public function user() { return $this->belongsTo(User::class); }
    public function course() { return $this->belongsTo(Course::class); }
}
