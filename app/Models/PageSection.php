<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PageSection extends Model
{
    protected $fillable = ['page_id', 'section_key', 'section_type', 'title', 'content', 'settings', 'sort_order', 'is_active'];
    protected $casts = ['settings' => 'array', 'is_active' => 'boolean'];

    public function page() { return $this->belongsTo(Page::class); }
}
