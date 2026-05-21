<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enrollment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'course_id', 'status', 'payment_status',
        'amount_paid', 'currency', 'coupon_id', 'discount_amount',
        'progress_percent', 'enrolled_at', 'completed_at', 'expires_at',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime', 'completed_at' => 'datetime', 'expires_at' => 'datetime',
        'amount_paid' => 'decimal:2', 'discount_amount' => 'decimal:2',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function course() { return $this->belongsTo(Course::class); }
    public function coupon() { return $this->belongsTo(Coupon::class); }
    public function payments() { return $this->hasMany(Payment::class); }
    public function certificate() { return $this->hasOne(Certificate::class); }
    public function progress() { return $this->hasMany(StudentProgress::class, 'user_id', 'user_id'); }
}
