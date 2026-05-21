<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class HeroBanner extends Model
{
    protected $fillable = [
        'title', 'subtitle', 'description', 'image', 'video_url',
        'primary_btn_text', 'primary_btn_url', 'secondary_btn_text', 'secondary_btn_url',
        'badge_text', 'stats', 'sort_order', 'is_active',
    ];
    protected $casts = ['stats' => 'array', 'is_active' => 'boolean'];

    public function scopeActive($q) { return $q->where('is_active', true)->orderBy('sort_order'); }
}
