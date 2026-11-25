<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Instructor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class InstructorProfileController extends Controller
{
    public function index()
    {
        $user       = auth()->user();
        $instructor = Instructor::firstOrCreate(['user_id' => $user->id]);
        return view('instructor.profile', compact('user', 'instructor'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone'      => 'nullable|string|max:30',
            'bio'        => 'nullable|string|max:2000',  // stored as instructor.description
            'avatar'     => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        } else {
            unset($data['avatar']);
        }

        $bio = $data['bio'] ?? null;
        unset($data['bio']);

        $user->update($data);

        // Update instructor bio
        $instructor = Instructor::firstOrCreate(['user_id' => $user->id]);
        $instructor->update(['description' => $bio]);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password'         => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.'])->withInput();
        }

        $user->update(['password' => Hash::make($request->password)]);
        return back()->with('password_success', 'Password changed successfully.');
    }
}
