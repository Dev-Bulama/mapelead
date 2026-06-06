<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    protected $fillable = [
        'user_id', 'title', 'expertise', 'description', 'signature_image',
        'hourly_rate', 'revenue_share_percent', 'is_featured', 'is_verified',
        'total_students', 'total_courses', 'average_rating',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_verified' => 'boolean',
        'revenue_share_percent' => 'decimal:2',
        'average_rating' => 'decimal:2',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function courses() { return $this->hasMany(Course::class); }
    public function sessionAssignments() { return $this->hasMany(CourseInstructor::class); }

    public function getFullNameAttribute() { return $this->user?->full_name ?? ''; }
    public function getAvatarUrlAttribute() { return $this->user?->avatar_url ?? ''; }
}
