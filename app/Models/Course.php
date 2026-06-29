<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Course extends Model
{
    use SoftDeletes, HasSlug;

    protected $fillable = [
        'instructor_id', 'category_id', 'title', 'slug', 'short_description', 'description',
        'thumbnail', 'brochure', 'promo_video', 'type', 'level', 'status', 'language',
        'duration_hours', 'duration_weeks', 'location', 'start_date', 'end_date',
        'max_students', 'price', 'discount_price', 'currency', 'is_free', 'is_featured',
        'certificate_enabled', 'is_published', 'published_at',
        'requirements', 'what_you_learn', 'who_is_this_for',
        'total_students', 'total_reviews', 'average_rating', 'total_lessons', 'total_duration_minutes',
        'meta_title', 'meta_description', 'meta_keywords',
    ];

    protected $casts = [
        'is_free' => 'boolean', 'is_featured' => 'boolean',
        'certificate_enabled' => 'boolean', 'is_published' => 'boolean',
        'published_at' => 'datetime', 'start_date' => 'datetime', 'end_date' => 'datetime',
        'price' => 'decimal:2', 'discount_price' => 'decimal:2', 'average_rating' => 'decimal:2',
        'requirements' => 'array', 'what_you_learn' => 'array', 'who_is_this_for' => 'array',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('title')->saveSlugsTo('slug');
    }

    public function instructor() { return $this->belongsTo(Instructor::class); }
    public function courseInstructors() { return $this->hasMany(CourseInstructor::class)->orderBy('sort_order'); }
    public function category() { return $this->belongsTo(CourseCategory::class, 'category_id'); }
    public function modules() { return $this->hasMany(CourseModule::class)->orderBy('sort_order'); }
    public function lessons() { return $this->hasMany(Lesson::class); }
    public function enrollments() { return $this->hasMany(Enrollment::class); }
    public function reviews() { return $this->hasMany(CourseReview::class)->where('is_approved', true); }
    public function tags() { return $this->belongsToMany(CourseTag::class, 'course_tag_pivot'); }

    public function scopePublished($q) { return $q->where('status', 'published')->where('is_published', true); }
    public function scopeFeatured($q) { return $q->where('is_featured', true); }

    public function getEffectivePriceAttribute(): float
    {
        return $this->discount_price && $this->discount_price < $this->price
            ? (float) $this->discount_price
            : (float) $this->price;
    }

    public function getThumbnailUrlAttribute(): string
    {
        if ($this->thumbnail) {
            return str_starts_with($this->thumbnail, 'http')
                ? $this->thumbnail
                : asset('storage/' . $this->thumbnail);
        }
        return self::placeholderDataUri();
    }

    public static function placeholderDataUri(): string
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="800" height="450" viewBox="0 0 800 450">'
             . '<rect width="800" height="450" fill="#f3f4f6"/>'
             . '<rect x="348" y="175" width="104" height="80" rx="8" fill="none" stroke="#d1d5db" stroke-width="3"/>'
             . '<circle cx="370" cy="197" r="8" fill="#d1d5db"/>'
             . '<polyline points="348,235 385,205 415,228 438,210 452,255" fill="none" stroke="#d1d5db" stroke-width="3" stroke-linejoin="round"/>'
             . '</svg>';
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    public function getBrochureUrlAttribute(): ?string
    {
        if (!$this->brochure) return null;
        return str_starts_with($this->brochure, 'http')
            ? $this->brochure
            : asset('storage/' . $this->brochure);
    }
}
