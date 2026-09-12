@extends('layouts.auth')
@section('title', 'Two-Factor Authentication')

@section('content')
<div class="text-center mb-6">
    <div class="w-14 h-14 bg-brand-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
        <svg class="w-7 h-7 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Two-Factor Authentication</h1>
    <p class="text-gray-500 mt-1 text-sm">Enter the 6-digit code from your authenticator app</p>
</div>

@if($errors->any())
<div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-4">
    {{ $errors->first() }}
</div>
@endif

<form method="POST" action="{{ route('auth.2fa.challenge.post') }}" class="space-y-4">
    @csrf
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Authentication Code</label>
        <input type="text" name="code" inputmode="numeric" pattern="[0-9]*" maxlength="8" autofocus
               class="w-full border border-gray-200 rounded-xl px-4 py-3 text-center text-2xl tracking-widest font-mono focus:outline-none focus:ring-2 focus:ring-brand-500"
               placeholder="000000">
    </div>
    <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold py-3 rounded-xl transition-colors">
        Verify
    </button>
</form>

<p class="text-center text-xs text-gray-400 mt-4">
    Can't access your authenticator app?
    <a href="{{ route('auth.login') }}" class="text-brand-600 hover:underline">Back to login</a>
</p>
@endsection
