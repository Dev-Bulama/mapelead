<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->configureRateLimiting();
    }

    protected function configureRateLimiting(): void
    {
        // Login: 5 attempts per minute per IP
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip())->response(function () {
                return back()->withErrors(['email' => 'Too many login attempts. Please try again in a minute.']);
            });
        });

        // Registration: 3 per minute per IP
        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip());
        });

        // Password reset: 3 per 5 minutes per IP
        RateLimiter::for('password-reset', function (Request $request) {
            return Limit::perMinutes(5, 3)->by($request->ip());
        });

        // Contact form: 5 per hour per IP
        RateLimiter::for('contact', function (Request $request) {
            return Limit::perHour(5)->by($request->ip());
        });

        // Newsletter: 3 per hour per IP
        RateLimiter::for('newsletter', function (Request $request) {
            return Limit::perHour(3)->by($request->ip());
        });

        // Course review: 3 per hour per user
        RateLimiter::for('review', function (Request $request) {
            return Limit::perHour(3)->by($request->user()?->id ?? $request->ip());
        });

        // Blog comment: 5 per hour per user/IP
        RateLimiter::for('comment', function (Request $request) {
            return Limit::perHour(5)->by($request->user()?->id ?? $request->ip());
        });

        // API: 60 per minute per token/IP
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?? $request->ip());
        });

        // OTP verification: 10 per 10 minutes per IP
        RateLimiter::for('otp', function (Request $request) {
            return Limit::perMinutes(10, 10)->by($request->ip());
        });
    }
}
