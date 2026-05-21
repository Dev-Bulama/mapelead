<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('auth.login')->withErrors(['email' => 'Google login failed. Please try again.']);
        }

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            $user->update(['google_id' => $googleUser->getId(), 'google_token' => $googleUser->token]);
        } else {
            $nameParts = explode(' ', $googleUser->getName(), 2);
            $user = User::create([
                'first_name'        => $nameParts[0],
                'last_name'         => $nameParts[1] ?? '',
                'email'             => $googleUser->getEmail(),
                'google_id'         => $googleUser->getId(),
                'google_token'      => $googleUser->token,
                'avatar'            => $googleUser->getAvatar(),
                'email_verified_at' => now(),
                'status'            => 'active',
            ]);
            $user->assignRole('student');
        }

        Auth::login($user, true);
        $user->update(['last_login_at' => now(), 'last_login_ip' => request()->ip()]);

        if ($user->isAdmin())      return redirect()->route('admin.dashboard');
        if ($user->isInstructor()) return redirect()->route('instructor.dashboard');
        return redirect()->route('student.dashboard');
    }
}
