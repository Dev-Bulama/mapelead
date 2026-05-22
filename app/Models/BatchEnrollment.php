<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchEnrollment extends Model
{
    protected $fillable = ['batch_id', 'enrollment_id', 'joined_at'];
    protected $casts = ['joined_at' => 'datetime'];
    public function batch() { return $this->belongsTo(Batch::class); }
    public function enrollment() { return $this->belongsTo(Enrollment::class); }
}
