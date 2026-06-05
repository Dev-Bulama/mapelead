<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    protected $fillable = [
        'name', 'position', 'department', 'bio', 'photo',
        'email', 'linkedin_url', 'twitter_url', 'github_url',
        'sort_order', 'is_featured', 'is_active',
    ];
    protected $casts = ['is_featured' => 'boolean', 'is_active' => 'boolean'];

    public function scopeActive($q) { return $q->where('is_active', true); }
    public function scopeFeatured($q) { return $q->where('is_featured', true); }

    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo) {
            return str_starts_with($this->photo, 'http')
                ? $this->photo
                : asset('storage/' . $this->photo);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=14215B&color=fff&size=200';
    }
}
