<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'description', 'type', 'value', 'minimum_order',
        'maximum_discount', 'usage_limit', 'used_count', 'starts_at', 'expires_at', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean', 'value' => 'decimal:2',
        'minimum_order' => 'decimal:2', 'maximum_discount' => 'decimal:2',
        'starts_at' => 'datetime', 'expires_at' => 'datetime',
    ];

    public function isValid(): bool
    {
        if (!$this->is_active) return false;
        if ($this->starts_at && now()->lt($this->starts_at)) return false;
        if ($this->expires_at && now()->gt($this->expires_at)) return false;
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) return false;
        return true;
    }

    public function calculateDiscount(float $amount): float
    {
        if ($this->type === 'percentage') {
            $discount = $amount * ($this->value / 100);
            return $this->maximum_discount ? min($discount, $this->maximum_discount) : $discount;
        }
        return min($this->value, $amount);
    }
}
