<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone', 'source', 'source_page',
        'campaign', 'interest', 'status', 'notes', 'assigned_to', 'last_contacted_at',
        'ip_address', 'country',
    ];
    protected $casts = ['last_contacted_at' => 'datetime'];

    public function assignedTo() { return $this->belongsTo(User::class, 'assigned_to'); }
    public function scopeNew($q) { return $q->where('status', 'new'); }
}
