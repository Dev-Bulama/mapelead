<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Page extends Model
{
    use SoftDeletes, HasSlug;

    protected $fillable = [
        'user_id', 'title', 'slug', 'content', 'template', 'status',
        'featured_image', 'published_at', 'scheduled_at', 'show_in_nav',
        'sort_order', 'is_homepage', 'meta_title', 'meta_description', 'meta_keywords',
        'og_image', 'schema_markup', 'revision',
    ];

    protected $casts = [
        'published_at' => 'datetime', 'scheduled_at' => 'datetime',
        'show_in_nav' => 'boolean', 'is_homepage' => 'boolean',
    ];

    public function getSlugOptions(): SlugOptions {
        return SlugOptions::create()->generateSlugsFrom('title')->saveSlugsTo('slug');
    }

    public function sections() { return $this->hasMany(PageSection::class)->orderBy('sort_order'); }
    public function author() { return $this->belongsTo(User::class, 'user_id'); }

    public function scopePublished($q) { return $q->where('status', 'published'); }
}
