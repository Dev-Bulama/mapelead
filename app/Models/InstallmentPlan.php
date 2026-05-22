<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstallmentPlan extends Model
{
    protected $fillable = [
        'enrollment_id', 'user_id', 'course_id',
        'total_amount', 'down_payment', 'amount_paid', 'outstanding_balance',
        'installment_count', 'grace_period_days', 'auto_lock_on_overdue', 'status',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'down_payment' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'outstanding_balance' => 'decimal:2',
        'auto_lock_on_overdue' => 'boolean',
    ];

    public function enrollment() { return $this->belongsTo(Enrollment::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function course() { return $this->belongsTo(Course::class); }
    public function schedule() { return $this->hasMany(InstallmentSchedule::class, 'plan_id')->orderBy('installment_number'); }
    public function pendingInstallments() { return $this->schedule()->whereIn('status', ['pending', 'overdue']); }
    public function nextDue() { return $this->pendingInstallments()->orderBy('due_date')->first(); }

    public function isFullyPaid(): bool { return $this->outstanding_balance <= 0; }
    public function isOverdue(): bool { return $this->status === 'overdue'; }
}
