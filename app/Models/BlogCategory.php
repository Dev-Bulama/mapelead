<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class BlogCategory extends Model
{
    use HasSlug;
    protected $fillable = ['name', 'slug', 'description', 'image', 'color', 'parent_id', 'sort_order', 'is_active', 'meta_title', 'meta_description'];
    protected $casts = ['is_active' => 'boolean'];

    public function getSlugOptions(): SlugOptions {
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug');
    }
    public function posts() { return $this->hasMany(BlogPost::class, 'category_id'); }
    public function parent() { return $this->belongsTo(BlogCategory::class, 'parent_id'); }
}
