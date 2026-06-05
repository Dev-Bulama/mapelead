<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class MediaLibrary extends Model
{
    use SoftDeletes;
    protected $table = 'media_library';
    protected $fillable = ['user_id', 'name', 'file_name', 'mime_type', 'disk', 'path', 'url', 'size', 'alt_text', 'caption', 'folder', 'conversions'];
    protected $casts = ['conversions' => 'array', 'size' => 'integer'];

    // Always generate URL from path so stale APP_URL in stored field doesn't break images
    public function getUrlAttribute(): string
    {
        if ($this->path) {
            return Storage::disk($this->disk ?? 'public')->url($this->path);
        }
        return $this->attributes['url'] ?? '';
    }

    public function getSizeForHumansAttribute(): string
    {
        $bytes = $this->size;
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}
