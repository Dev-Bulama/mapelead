<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enrollment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'course_id', 'status', 'payment_status', 'payment_type',
        'training_type', 'amount_paid', 'currency', 'coupon_id', 'discount_amount',
        'progress_percent', 'enrolled_at', 'completed_at', 'expires_at',
        'access_locked', 'access_locked_at', 'access_locked_reason',
    ];

    public function getTrainingTypeLabelAttribute(): string
    {
        return match($this->training_type) {
            'physical_monthly'   => 'Physical (1 Month · 3×/Day)',
            'physical_quarterly' => 'Physical (3 Months)',
            default              => 'Online',
        };
    }

    protected $casts = [
        'enrolled_at'     => 'datetime',
        'completed_at'    => 'datetime',
        'expires_at'      => 'datetime',
        'access_locked_at'=> 'datetime',
        'amount_paid'     => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'access_locked'   => 'boolean',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function course() { return $this->belongsTo(Course::class); }
    public function coupon() { return $this->belongsTo(Coupon::class); }
    public function payments() { return $this->hasMany(Payment::class); }
    public function certificate() { return $this->hasOne(Certificate::class); }
    public function progress() { return $this->hasMany(StudentProgress::class, 'user_id', 'user_id'); }
    public function batch() { return $this->hasOne(BatchEnrollment::class)->with('batch'); }
    public function installmentPlan() { return $this->hasOne(InstallmentPlan::class); }
    public function attendanceRecords() { return $this->hasMany(AttendanceRecord::class); }

    public function isLocked(): bool { return (bool) $this->access_locked; }
    public function isInstallment(): bool { return $this->payment_type === 'installment'; }

    public function hasActiveAccess(): bool
    {
        return !$this->access_locked && in_array($this->status, ['active', 'completed']);
    }

    public function getOutstandingBalance(): float
    {
        if ($this->payment_type === 'full') return 0;
        return (float) ($this->installmentPlan?->outstanding_balance ?? 0);
    }
}
