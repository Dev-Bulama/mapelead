<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    protected $fillable = ['session_id', 'user_id', 'enrollment_id', 'status', 'check_in_time', 'notes', 'marked_by'];
    protected $casts = ['check_in_time' => 'datetime'];
    public function session() { return $this->belongsTo(AttendanceSession::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function enrollment() { return $this->belongsTo(Enrollment::class); }
    public function markedBy() { return $this->belongsTo(User::class, 'marked_by'); }
}
