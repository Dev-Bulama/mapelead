<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ScriptInjection extends Model
{
    protected $fillable = ['name', 'provider', 'location', 'code', 'is_active', 'pages'];
    protected $casts = ['is_active' => 'boolean'];

    public function scopeActive($q) { return $q->where('is_active', true); }
}
