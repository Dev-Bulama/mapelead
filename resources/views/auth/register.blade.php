@extends('layouts.auth')
@section('title', 'Create Account')

@section('content')
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Create your account</h1>
        <p class="text-gray-500 mt-1 text-sm">Join 10,000+ students learning in-demand tech skills</p>
    </div>

    <a href="{{ route('auth.google') }}"
       class="flex items-center justify-center gap-3 w-full border-2 border-gray-200 hover:border-gray-300 rounded-xl py-3 font-medium text-gray-700 transition-colors mb-6">
        <svg class="w-5 h-5" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
        </svg>
        Sign up with Google
    </a>

    <div class="flex items-center gap-3 mb-6">
        <div class="flex-1 h-px bg-gray-200"></div>
        <span class="text-gray-400 text-sm">or with email</span>
        <div class="flex-1 h-px bg-gray-200"></div>
    </div>

    <form action="{{ route('auth.register.post') }}" method="POST" x-data="{ loading: false }" @submit="loading = true">
        @csrf
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required
                           class="w-full border {{ $errors->has('first_name') ? 'border-red-400' : 'border-gray-300' }} rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                    @error('first_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required
                           class="w-full border {{ $errors->has('last_name') ? 'border-red-400' : 'border-gray-300' }} rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full border {{ $errors->has('email') ? 'border-red-400' : 'border-gray-300' }} rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number <span class="text-gray-400">(optional)</span></label>
                <input type="tel" name="phone" value="{{ old('phone') }}"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>

            <div x-data="{ show: false }">
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'" name="password" required minlength="8"
                           class="w-full border {{ $errors->has('password') ? 'border-red-400' : 'border-gray-300' }} rounded-xl px-4 py-3 pr-12 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                </div>
                @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation" required
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>

            <div class="flex items-start gap-2">
                <input type="checkbox" id="terms" required class="w-4 h-4 mt-0.5 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                <label for="terms" class="text-sm text-gray-600">
                    I agree to the <a href="/terms-of-service" class="text-brand-600 hover:underline">Terms of Service</a> and
                    <a href="/privacy-policy" class="text-brand-600 hover:underline">Privacy Policy</a>
                </label>
            </div>
        </div>

        <button type="submit" :disabled="loading"
                class="w-full mt-6 bg-brand-600 hover:bg-brand-700 text-white font-semibold py-3 rounded-xl transition-colors flex items-center justify-center gap-2 disabled:opacity-70">
            <svg x-show="loading" x-cloak class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
            <span x-text="loading ? 'Creating account...' : 'Create Free Account'">Create Free Account</span>
        </button>
    </form>
@endsection

@section('footer_link')
    Already have an account? <a href="{{ route('auth.login') }}" class="text-white font-semibold hover:underline">Sign in</a>
@endsection
