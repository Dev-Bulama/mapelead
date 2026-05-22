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

        <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-semibold px-8 py-3 rounded-xl transition-colors">
            Save Changes
        </button>
    </form>
</div>
@endsection
