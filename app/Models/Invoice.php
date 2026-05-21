<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'user_id', 'payment_id', 'invoice_number',
        'subtotal', 'discount', 'tax', 'total', 'currency',
        'status', 'line_items', 'notes', 'due_date', 'paid_at',
    ];

    protected $casts = [
        'line_items' => 'array',
        'subtotal' => 'decimal:2', 'discount' => 'decimal:2',
        'tax' => 'decimal:2', 'total' => 'decimal:2',
        'due_date' => 'datetime', 'paid_at' => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function payment() { return $this->belongsTo(Payment::class); }
}
