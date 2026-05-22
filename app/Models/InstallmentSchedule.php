<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstallmentSchedule extends Model
{
    protected $fillable = [
        'plan_id', 'installment_number', 'amount', 'due_date',
        'amount_paid', 'paid_at', 'payment_id', 'status', 'reminder_sent',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'reminder_sent' => 'boolean',
    ];

    public function plan() { return $this->belongsTo(InstallmentPlan::class, 'plan_id'); }
    public function payment() { return $this->belongsTo(Payment::class); }

    public function isOverdue(): bool {
        return $this->status !== 'paid' && $this->due_date->isPast();
    }
}
