<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id', 'enrollment_id', 'reference', 'gateway', 'gateway_reference',
        'amount', 'currency', 'status', 'payment_method', 'gateway_response', 'notes', 'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
        'paid_at' => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function enrollment() { return $this->belongsTo(Enrollment::class); }
    public function invoice() { return $this->hasOne(Invoice::class); }
}
