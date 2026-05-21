<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        'name', 'title', 'company', 'avatar', 'content', 'rating',
        'course_name', 'course_id', 'video_url', 'is_featured', 'is_active', 'sort_order',
    ];
    protected $casts = ['is_featured' => 'boolean', 'is_active' => 'boolean'];

    public function course() { return $this->belongsTo(Course::class); }
    public function scopeActive($q) { return $q->where('is_active', true); }
    public function scopeFeatured($q) { return $q->where('is_featured', true); }
}
