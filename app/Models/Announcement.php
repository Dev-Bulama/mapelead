<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'type', 'title', 'message', 'url', 'url_text',
        'bg_color', 'text_color', 'is_active', 'is_dismissible',
        'starts_at', 'ends_at',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'is_dismissible' => 'boolean',
        'starts_at'      => 'datetime',
        'ends_at'        => 'datetime',
    ];

    public function scopeActive($q) { return $q->where('is_active', true); }
}
