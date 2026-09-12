@extends('layouts.auth')
@section('title', 'Set Up Two-Factor Authentication')

@section('content')
<div class="text-center mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Enable Two-Factor Auth</h1>
    <p class="text-gray-500 mt-1 text-sm">Scan the QR code with your authenticator app, then enter the code below</p>
</div>

@if($errors->any())
<div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-4">
    {{ $errors->first() }}
</div>
@endif

<div class="flex flex-col items-center gap-4 mb-6">
    <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm">
        <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="2FA QR Code" class="w-44 h-44">
    </div>
    <div class="text-center">
        <p class="text-xs text-gray-500 mb-1">Or enter this secret manually:</p>
        <code class="bg-gray-100 text-gray-800 text-sm font-mono px-3 py-1.5 rounded-lg tracking-widest">{{ $secret }}</code>
    </div>
</div>

<form method="POST" action="{{ route('2fa.enable') }}" class="space-y-4">
    @csrf
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Verification Code</label>
        <input type="text" name="code" inputmode="numeric" maxlength="6" autofocus
               class="w-full border border-gray-200 rounded-xl px-4 py-3 text-center text-xl tracking-widest font-mono focus:outline-none focus:ring-2 focus:ring-brand-500"
               placeholder="000000">
        @error('code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold py-3 rounded-xl transition-colors">
        Enable 2FA
    </button>
</form>
@endsection
