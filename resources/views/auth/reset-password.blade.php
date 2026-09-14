@extends('layouts.auth')
@section('title', 'Set New Password')
@section('content')
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Set new password</h1>
    </div>
    <form action="{{ route('auth.reset.post') }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                <input type="password" name="password" required minlength="8" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                @if(!$errors->has('password'))
                <p class="text-xs text-gray-400 mt-1">Min 8 chars · uppercase · lowercase · number · special character</p>
                @endif
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation" required class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>
        </div>
        <button type="submit" class="w-full mt-6 bg-brand-600 hover:bg-brand-700 text-white font-semibold py-3 rounded-xl transition-colors">
            Reset Password
        </button>
    </form>
@endsection
