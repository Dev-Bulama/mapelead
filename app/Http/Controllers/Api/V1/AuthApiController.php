<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use PragmaRX\Google2FA\Google2FA;

class AuthApiController extends Controller
{
    use ApiResponseTrait;

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'       => 'required|email',
            'password'    => 'required|string',
            'totp_code'   => 'nullable|string|size:6',
            'device_name' => 'nullable|string|max:255',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->error('Invalid credentials', 401);
        }

        if ($user->portal_locked) {
            return $this->error(
                'Your account has been locked. ' . ($user->portal_locked_reason ?? 'Please contact support.'),
                403
            );
        }

        if ($user->two_factor_enabled) {
            if (empty($request->totp_code)) {
                return $this->error('Two-factor authentication code required', 422, ['requires_2fa' => true]);
            }
            $google2fa = new Google2FA();
            $secret = decrypt($user->two_factor_secret);
            if (!$google2fa->verifyKey($secret, $request->totp_code)) {
                return $this->error('Invalid two-factor authentication code', 422);
            }
        }

        $deviceName = $request->device_name ?? ($request->userAgent() ?? 'mobile-app');
        $token = $user->createToken($deviceName)->plainTextToken;

        $user->update(['last_login_at' => now()]);

        return $this->success([
            'token' => $token,
            'user'  => $this->formatUser($user),
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'first_name'  => 'required|string|max:100',
            'last_name'   => 'required|string|max:100',
            'email'       => 'required|email|max:255|unique:users,email',
            'phone'       => 'nullable|string|max:20',
            'password'    => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
            'device_name' => 'nullable|string|max:255',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'password'   => $request->password,
        ]);

        $user->assignRole('student');
        event(new Registered($user));

        $deviceName = $request->device_name ?? ($request->userAgent() ?? 'mobile-app');
        $token = $user->createToken($deviceName)->plainTextToken;

        return $this->success([
            'token' => $token,
            'user'  => $this->formatUser($user),
        ], 201);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return $this->success([], message: 'Logged out successfully');
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        return $this->success($this->formatUser($user, detailed: true));
    }

    public function resendVerification(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user->hasVerifiedEmail()) {
            return $this->error('Email is already verified', 400);
        }
        $user->sendEmailVerificationNotification();
        return $this->success([], message: 'Verification email sent');
    }

    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'password'         => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->error('Current password is incorrect', 422);
        }

        $user->update(['password' => $request->password]);

        return $this->success([], message: 'Password changed successfully');
    }

    private function formatUser(User $user, bool $detailed = false): array
    {
        $data = [
            'id'                 => $user->id,
            'first_name'         => $user->first_name,
            'last_name'          => $user->last_name,
            'full_name'          => $user->full_name,
            'email'              => $user->email,
            'phone'              => $user->phone,
            'avatar_url'         => $user->avatar_url,
            'email_verified'     => !is_null($user->email_verified_at),
            'two_factor_enabled' => (bool) $user->two_factor_enabled,
            'roles'              => $user->getRoleNames()->values(),
            'admission_number'   => $user->admission_number,
        ];

        if ($detailed) {
            $data['enrollments_count']  = $user->enrollments()->where('payment_status', 'paid')->count();
            $data['completed_courses']  = $user->enrollments()->where('status', 'completed')->count();
            $data['created_at']         = $user->created_at?->toDateString();
        }

        return $data;
    }
}
