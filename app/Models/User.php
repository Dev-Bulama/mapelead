<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles, LogsActivity;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone', 'avatar',
        'password', 'status', 'gender', 'date_of_birth',
        'country', 'state', 'city', 'bio',
        'linkedin_url', 'twitter_url', 'website_url',
        'google_id', 'google_token',
        'last_login_at', 'last_login_ip',
        'two_factor_enabled', 'two_factor_secret',
        'admission_number', 'portal_locked', 'portal_locked_reason', 'portal_locked_at',
    ];

    protected $hidden = ['password', 'remember_token', 'two_factor_secret', 'google_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at'  => 'datetime',
            'last_login_at'      => 'datetime',
            'portal_locked_at'   => 'datetime',
            'date_of_birth'      => 'date',
            'password'           => 'hashed',
            'two_factor_enabled' => 'boolean',
            'portal_locked'      => 'boolean',
        ];
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return str_starts_with($this->avatar, 'http')
                ? $this->avatar
                : asset('storage/' . $this->avatar);
        }
        $initial = strtoupper(substr($this->first_name, 0, 1));
        return "https://ui-avatars.com/api/?name={$initial}&background=6366f1&color=fff&size=128";
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function instructor()
    {
        return $this->hasOne(Instructor::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function progress()
    {
        return $this->hasMany(StudentProgress::class);
    }

    public function blogPosts()
    {
        return $this->hasMany(BlogPost::class);
    }

    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function notificationsLog()
    {
        return $this->hasMany(NotificationLog::class);
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function assignmentSubmissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function installmentPlans()
    {
        return $this->hasMany(InstallmentPlan::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeStudents($query)
    {
        return $query->whereHas('roles', fn($q) => $q->where('name', 'student'));
    }

    // ─── Activity Log ─────────────────────────────────────────────────────────

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['first_name', 'last_name', 'email', 'status'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->hasAnyRole(['super_admin', 'admin']);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function isStudent(): bool
    {
        return $this->hasRole('student');
    }

    public function isInstructor(): bool
    {
        return $this->hasRole('instructor');
    }

    public function isPortalLocked(): bool
    {
        return (bool) $this->portal_locked;
    }

    public function getAttendancePercentageForCourse(int $courseId): float
    {
        $sessions = AttendanceSession::where('course_id', $courseId)->count();
        if ($sessions === 0) return 100.0;

        $present = AttendanceRecord::where('user_id', $this->id)
            ->whereHas('session', fn($q) => $q->where('course_id', $courseId))
            ->whereIn('status', ['present', 'late'])
            ->count();

        return round(($present / $sessions) * 100, 2);
    }
}
