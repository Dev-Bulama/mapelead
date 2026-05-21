<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class BlogPost extends Model
{
    use SoftDeletes, HasSlug;

    protected $fillable = [
        'user_id', 'category_id', 'title', 'slug', 'excerpt', 'content',
        'featured_image', 'status', 'is_featured', 'allow_comments', 'views',
        'read_time_minutes', 'published_at', 'scheduled_at',
        'meta_title', 'meta_description', 'meta_keywords', 'og_image',
    ];

    protected $casts = [
        'is_featured' => 'boolean', 'allow_comments' => 'boolean',
        'published_at' => 'datetime', 'scheduled_at' => 'datetime',
    ];

    public function getSlugOptions(): SlugOptions {
        return SlugOptions::create()->generateSlugsFrom('title')->saveSlugsTo('slug');
    }

    public function author() { return $this->belongsTo(User::class, 'user_id'); }
    public function category() { return $this->belongsTo(BlogCategory::class, 'category_id'); }
    public function tags() { return $this->belongsToMany(BlogTag::class, 'blog_post_tags'); }
    public function comments() { return $this->hasMany(BlogComment::class)->where('status', 'approved')->whereNull('parent_id'); }

    public function scopePublished($q) { return $q->where('status', 'published'); }
    public function scopeFeatured($q) { return $q->where('is_featured', true); }

    public function getFeaturedImageUrlAttribute(): string {
        if ($this->featured_image) return asset('storage/'.$this->featured_image);
        return asset('images/blog-placeholder.jpg');
    }
}
