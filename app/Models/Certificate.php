<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = [
        'user_id', 'course_id', 'enrollment_id',
        'certificate_number', 'template', 'file_path', 'issued_at', 'expires_at',
    ];

    protected $casts = ['issued_at' => 'datetime', 'expires_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }
    public function course() { return $this->belongsTo(Course::class); }
    public function enrollment() { return $this->belongsTo(Enrollment::class); }
}
