@extends('layouts.student')
@section('title', 'My Profile')
@section('page_title', 'Profile Settings')

@section('content')
<div class="max-w-3xl space-y-6">

    {{-- Profile Card --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <div class="flex items-center gap-5 mb-6">
            <div class="relative">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->full_name }}" class="w-20 h-20 rounded-2xl object-cover ring-4 ring-brand-100">
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $user->full_name }}</h2>
                <p class="text-gray-500 text-sm">{{ $user->email }}</p>
                <div class="flex gap-2 mt-2">
                    @foreach($user->getRoleNames() as $role)
                        <span class="text-xs bg-brand-100 text-brand-700 font-medium px-2.5 py-1 rounded-full capitalize">{{ str_replace('_', ' ', $role) }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Form --}}
    <form action="{{ route('student.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl p-4 text-sm">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h3 class="font-bold text-gray-900 mb-5 pb-3 border-b border-gray-100">Personal Information</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach(['first_name' => 'First Name', 'last_name' => 'Last Name'] as $field => $label)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
                        <input type="text" name="{{ $field }}" value="{{ old($field, $user->$field) }}" required
                               class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                @endforeach
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                    <input type="text" name="country" value="{{ old('country', $user->country) }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                    <textarea name="bio" rows="3" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none">{{ old('bio', $user->bio) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">LinkedIn URL</label>
                    <input type="url" name="linkedin_url" value="{{ old('linkedin_url', $user->linkedin_url) }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Twitter URL</label>
                    <input type="url" name="twitter_url" value="{{ old('twitter_url', $user->twitter_url) }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Profile Photo</label>
                    <input type="file" name="avatar" accept="image/*" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none">
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG, GIF up to 2MB</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h3 class="font-bold text-gray-900 mb-5 pb-3 border-b border-gray-100">Change Password</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                    <input type="password" name="current_password" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                    @error('current_password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                    <input type="password" name="new_password" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                    <input type="password" name="new_password_confirmation" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
            </div>
        </div>

        <p class="text-xs text-gray-400">Password must be at least 8 characters with uppercase, lowercase, a number, and a special character.</p>

        <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-semibold px-8 py-3 rounded-xl transition-colors">
            Save Changes
        </button>
    </form>

    {{-- Two-Factor Authentication --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6 mt-6">
        <h3 class="font-bold text-gray-900 mb-1">Two-Factor Authentication (2FA)</h3>
        <p class="text-sm text-gray-500 mb-5">Add an extra layer of security by requiring a code from your authenticator app when signing in.</p>

        @if($user->two_factor_enabled)
        <div class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl px-4 py-3 mb-4">
            <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            <span class="text-sm text-green-700 font-medium">2FA is enabled on your account</span>
        </div>
        <form method="POST" action="{{ route('2fa.disable') }}">
            @csrf
            <div class="flex items-end gap-3">
                <div class="flex-1 max-w-xs">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm your password to disable</label>
                    <input type="password" name="password" required
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-400">
                    @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-5 py-2.5 rounded-xl transition-colors text-sm">
                    Disable 2FA
                </button>
            </div>
        </form>
        @else
        <div class="flex items-center gap-3 bg-yellow-50 border border-yellow-200 rounded-xl px-4 py-3 mb-4">
            <svg class="w-5 h-5 text-yellow-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span class="text-sm text-yellow-700">2FA is not enabled. Your account has less protection.</span>
        </div>
        <a href="{{ route('2fa.setup') }}" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold px-5 py-2.5 rounded-xl transition-colors text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            Enable 2FA
        </a>
        @endif
    </div>
</div>
@endsection
