<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function showLogin()  { return view('auth.login'); }
    public function showRegister() { return view('auth.register'); }
    public function showForgotPassword() { return view('auth.forgot-password'); }
    public function showResetPassword(string $token) { return view('auth.reset-password', ['token' => $token]); }
    public function showOtp() { return view('auth.otp'); }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
        }

        $user = auth()->user();
        if ($user->status === 'suspended') {
            Auth::logout();
            return back()->withErrors(['email' => 'Your account has been suspended.']);
        }

        $user->update(['last_login_at' => now(), 'last_login_ip' => $request->ip()]);
        $request->session()->regenerate();
        return $this->redirectByRole($user);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email',
            'phone'      => 'nullable|string|max:20',
            'password'   => 'required|string|min:8|confirmed',
        ]);

        $user = User::create(array_merge($data, ['status' => 'active']));
        $user->assignRole('student');
        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('student.dashboard')->with('success', 'Welcome to MapeLearn!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $status = Password::sendResetLink($request->only('email'));
        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'Password reset link sent!')
            : back()->withErrors(['email' => __($status)]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => $password])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('auth.login')->with('success', 'Password reset successfully!')
            : back()->withErrors(['email' => [__($status)]]);
    }

    public function verifyEmail(Request $request, string $id, string $hash)
    {
        $user = User::findOrFail($id);
        if (!hash_equals(sha1($user->email), $hash)) abort(403);
        if (!$user->hasVerifiedEmail()) $user->markEmailAsVerified();
        return redirect()->route('student.dashboard')->with('success', 'Email verified!');
    }

    public function resendVerification(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('success', 'Verification email resent!');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|string']);
        $otp = OtpCode::where('identifier', session('otp_identifier'))
            ->where('code', $request->otp)
            ->where('type', session('otp_type', 'email_verification'))
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otp) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
        }

        $otp->update(['used' => true]);
        return redirect()->route('student.dashboard')->with('success', 'Verified successfully!');
    }

    private function redirectByRole(User $user): \Illuminate\Http\RedirectResponse
    {
        if ($user->isAdmin())      return redirect()->route('admin.dashboard');
        if ($user->isInstructor()) return redirect()->route('instructor.dashboard');
        return redirect()->route('student.dashboard');
    }
}
