<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class NavigationMenu extends Model
{
    protected $fillable = ['name', 'location', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function items() { return $this->hasMany(NavigationItem::class, 'menu_id')->whereNull('parent_id')->orderBy('sort_order'); }
    public function allItems() { return $this->hasMany(NavigationItem::class, 'menu_id')->orderBy('sort_order'); }
}
