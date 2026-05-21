<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class NavigationItem extends Model
{
    protected $fillable = ['menu_id', 'parent_id', 'label', 'url', 'route_name', 'icon', 'target', 'sort_order', 'is_active', 'is_external'];
    protected $casts = ['is_active' => 'boolean', 'is_external' => 'boolean'];

    public function menu() { return $this->belongsTo(NavigationMenu::class); }
    public function parent() { return $this->belongsTo(NavigationItem::class, 'parent_id'); }
    public function children() { return $this->hasMany(NavigationItem::class, 'parent_id')->orderBy('sort_order'); }

    public function getResolvedUrlAttribute(): string
    {
        if ($this->route_name && \Route::has($this->route_name)) {
            return route($this->route_name);
        }
        return $this->url ?? '#';
    }
}
