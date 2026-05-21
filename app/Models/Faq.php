<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $fillable = ['question', 'answer', 'category', 'sort_order', 'is_active', 'is_featured'];
    protected $casts = ['is_active' => 'boolean', 'is_featured' => 'boolean'];

    public function scopeActive($q) { return $q->where('is_active', true)->orderBy('sort_order'); }
}
