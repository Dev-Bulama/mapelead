<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileApiController extends Controller
{
    use ApiResponseTrait;

    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        return $this->success([
            'id'                 => $user->id,
            'first_name'         => $user->first_name,
            'last_name'          => $user->last_name,
            'full_name'          => $user->full_name,
            'email'              => $user->email,
            'phone'              => $user->phone,
            'date_of_birth'      => $user->date_of_birth?->toDateString(),
            'bio'                => $user->bio,
            'gender'             => $user->gender,
            'avatar_url'         => $user->avatar_url,
            'email_verified'     => !is_null($user->email_verified_at),
            'two_factor_enabled' => (bool) $user->two_factor_enabled,
            'admission_number'   => $user->admission_number,
            'roles'              => $user->getRoleNames()->values(),
            'created_at'         => $user->created_at?->toDateString(),
            'stats'              => [
                'enrollments'       => $user->enrollments()->where('payment_status', 'paid')->count(),
                'completed_courses' => $user->enrollments()->where('status', 'completed')->count(),
                'certificates'      => $user->certificates()->count(),
            ],
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'first_name'    => 'nullable|string|max:100',
            'last_name'     => 'nullable|string|max:100',
            'phone'         => 'nullable|string|max:20',
            'bio'           => 'nullable|string|max:1000',
            'date_of_birth' => 'nullable|date|before:today',
            'gender'        => 'nullable|in:male,female,other',
        ]);

        $user = $request->user();
        $user->update($request->only(['first_name', 'last_name', 'phone', 'bio', 'date_of_birth', 'gender']));

        return $this->success([
            'first_name' => $user->first_name,
            'last_name'  => $user->last_name,
            'phone'      => $user->phone,
            'bio'        => $user->bio,
            'full_name'  => $user->full_name,
        ], message: 'Profile updated successfully');
    }

    public function uploadAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,jpg,png,webp|max:2048',
        ]);

        $user = $request->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return $this->success([
            'avatar_url' => $user->avatar_url,
        ], message: 'Avatar updated successfully');
    }

    public function learningStats(Request $request): JsonResponse
    {
        $user = $request->user();

        $enrollments = $user->enrollments()
            ->where('payment_status', 'paid')
            ->with('course:id,title,slug,thumbnail')
            ->get();

        $totalMinutes = $user->progress()
            ->sum('watch_time_seconds') / 60;

        $streakDays = \App\Models\LearningStreak::where('user_id', $user->id)
            ->orderByDesc('streak_date')
            ->take(30)
            ->pluck('streak_date')
            ->toArray();

        $currentStreak = 0;
        $checkDate = now()->toDateString();
        foreach ($streakDays as $day) {
            if ($day === $checkDate) {
                $currentStreak++;
                $checkDate = now()->subDays($currentStreak)->toDateString();
            } else {
                break;
            }
        }

        return $this->success([
            'total_courses'         => $enrollments->count(),
            'completed_courses'     => $enrollments->where('status', 'completed')->count(),
            'in_progress_courses'   => $enrollments->where('status', 'active')->count(),
            'total_watch_minutes'   => round($totalMinutes, 1),
            'current_streak_days'   => $currentStreak,
            'certificates_earned'   => $user->certificates()->count(),
        ]);
    }
}
