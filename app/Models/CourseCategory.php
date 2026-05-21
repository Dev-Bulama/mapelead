<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class CourseCategory extends Model
{
    use SoftDeletes, HasSlug;

    protected $fillable = [
        'name', 'slug', 'description', 'icon', 'image',
        'parent_id', 'sort_order', 'is_active', 'is_featured',
        'meta_title', 'meta_description',
    ];

    protected $casts = ['is_active' => 'boolean', 'is_featured' => 'boolean'];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug');
    }

    public function parent() { return $this->belongsTo(CourseCategory::class, 'parent_id'); }
    public function children() { return $this->hasMany(CourseCategory::class, 'parent_id'); }
    public function courses() { return $this->hasMany(Course::class, 'category_id'); }

    public function scopeActive($q) { return $q->where('is_active', true); }
    public function scopeFeatured($q) { return $q->where('is_featured', true); }
}
