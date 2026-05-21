@extends('layouts.auth')
@section('title', 'Forgot Password')
@section('content')
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Reset your password</h1>
        <p class="text-gray-500 mt-1 text-sm">Enter your email and we'll send a reset link</p>
    </div>
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl p-4 mb-4 text-sm">{{ session('success') }}</div>
    @endif
    <form action="{{ route('auth.forgot.post') }}" method="POST">
        @csrf
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        <button type="submit" class="w-full mt-6 bg-brand-600 hover:bg-brand-700 text-white font-semibold py-3 rounded-xl transition-colors">
            Send Reset Link
        </button>
    </form>
@endsection
@section('footer_link')
    <a href="{{ route('auth.login') }}" class="text-white font-semibold hover:underline">← Back to Sign In</a>
@endsection
