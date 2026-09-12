@extends('layouts.auth')
@section('title', 'Verify Your Email')

@section('content')
<div class="text-center mb-6">
    <div class="w-14 h-14 bg-brand-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
        <svg class="w-7 h-7 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Verify your email</h1>
    <p class="text-gray-500 mt-1 text-sm">We've sent a verification link to <strong>{{ auth()->user()->email }}</strong></p>
</div>

@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3 mb-4">
    {{ session('success') }}
</div>
@endif

<p class="text-sm text-gray-600 text-center mb-6">
    Click the link in your email to activate your account. Check your spam folder if you don't see it.
</p>

<form method="POST" action="{{ route('verification.resend') }}">
    @csrf
    <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold py-3 rounded-xl transition-colors">
        Resend Verification Email
    </button>
</form>

<form method="POST" action="{{ route('auth.logout') }}" class="mt-3">
    @csrf
    <button type="submit" class="w-full text-sm text-gray-500 hover:text-gray-700 py-2 transition-colors">
        Sign out
    </button>
</form>
@endsection
