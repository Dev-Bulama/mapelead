<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstructorCode extends Model
{
    protected $fillable = [
        'code',
        'note',
        'is_active',
        'created_by_user_id',
        'used_by_user_id',
        'used_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'used_at'   => 'datetime',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function usedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'used_by_user_id');
    }
}
