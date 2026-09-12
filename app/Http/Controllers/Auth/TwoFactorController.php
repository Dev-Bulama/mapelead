<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\Request;

class TwoFactorController extends Controller
{
    public function showSetup(Request $request)
    {
        $user = auth()->user();
        if ($user->two_factor_enabled) {
            return redirect()->back()->with('info', '2FA is already enabled.');
        }

        $google2fa = app('pragmarx.google2fa');
        $secret = $google2fa->generateSecretKey();
        $request->session()->put('2fa_setup_secret', $secret);

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        $writer = new Writer(
            new ImageRenderer(
                new RendererStyle(200),
                new SvgImageBackEnd()
            )
        );
        $qrCode = base64_encode($writer->writeString($qrCodeUrl));

        return view('auth.2fa-setup', compact('secret', 'qrCode'));
    }

    public function enable(Request $request)
    {
        $request->validate(['code' => 'required|string|digits:6']);

        $secret = $request->session()->get('2fa_setup_secret');
        if (!$secret) return back()->withErrors(['code' => 'Session expired. Please restart setup.']);

        $google2fa = app('pragmarx.google2fa');
        if (!$google2fa->verifyKey($secret, $request->code)) {
            return back()->withErrors(['code' => 'Invalid code. Please try again.']);
        }

        auth()->user()->update([
            'two_factor_secret'  => encrypt($secret),
            'two_factor_enabled' => true,
        ]);

        $request->session()->forget('2fa_setup_secret');
        return redirect()->back()->with('success', 'Two-factor authentication enabled successfully!');
    }

    public function disable(Request $request)
    {
        $request->validate(['password' => 'required']);
        if (!\Illuminate\Support\Facades\Hash::check($request->password, auth()->user()->password)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        auth()->user()->update([
            'two_factor_secret'  => null,
            'two_factor_enabled' => false,
        ]);

        return redirect()->back()->with('success', 'Two-factor authentication disabled.');
    }

    public function showChallenge()
    {
        if (!session('2fa_user_id')) return redirect()->route('auth.login');
        return view('auth.2fa-challenge');
    }

    public function challenge(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $userId = session('2fa_user_id');
        if (!$userId) return redirect()->route('auth.login');

        $user = \App\Models\User::findOrFail($userId);
        $google2fa = app('pragmarx.google2fa');
        $secret = decrypt($user->two_factor_secret);

        if (!$google2fa->verifyKey($secret, $request->code)) {
            return back()->withErrors(['code' => 'Invalid authentication code.']);
        }

        $request->session()->forget('2fa_user_id');
        \Illuminate\Support\Facades\Auth::login($user);
        $request->session()->regenerate();

        $redirectPath = session()->pull('2fa_redirect');
        if ($redirectPath) return redirect($redirectPath);

        if ($user->isAdmin())      return redirect()->route('admin.dashboard');
        if ($user->isInstructor()) return redirect()->route('instructor.dashboard');
        return redirect()->route('student.dashboard');
    }
}
