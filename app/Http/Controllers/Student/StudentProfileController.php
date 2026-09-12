<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password as PasswordRules;

class StudentProfileController extends Controller
{
    public function index()
    {
        return view('student.profile', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'phone'      => 'nullable|string|max:20',
            'bio'        => 'nullable|string|max:500',
            'country'    => 'nullable|string|max:100',
            'state'      => 'nullable|string|max:100',
            'city'       => 'nullable|string|max:100',
            'linkedin_url' => 'nullable|url|max:255',
            'twitter_url'  => 'nullable|url|max:255',
            'avatar'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        } else {
            unset($data['avatar']);
        }

        if ($request->filled('current_password') && $request->filled('new_password')) {
            $request->validate([
                'current_password' => 'required',
                'new_password'     => ['required', 'confirmed', PasswordRules::min(8)->mixedCase()->numbers()->symbols()],
            ]);
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
            $data['password'] = Hash::make($request->new_password);
        }

        $user->update($data);
        return back()->with('success', 'Profile updated successfully!');
    }
}
