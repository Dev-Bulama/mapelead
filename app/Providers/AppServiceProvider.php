<?php

namespace App\Providers;

use App\Models\SiteSetting;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->configureFromDatabase();
        $this->configureRateLimiting();
    }

    protected function configureFromDatabase(): void
    {
        try {
            $s = SiteSetting::getGroup('integrations');
            if (empty($s)) return;

            // Mail / SMTP
            if (!empty($s['mail_mailer'])) {
                Config::set('mail.default', $s['mail_mailer']);
                Config::set('mail.mailers.smtp.host',       $s['mail_host']       ?? config('mail.mailers.smtp.host'));
                Config::set('mail.mailers.smtp.port',       $s['mail_port']       ?? config('mail.mailers.smtp.port'));
                Config::set('mail.mailers.smtp.username',   $s['mail_username']   ?? config('mail.mailers.smtp.username'));
                Config::set('mail.mailers.smtp.password',   $s['mail_password']   ?? config('mail.mailers.smtp.password'));
                Config::set('mail.mailers.smtp.encryption', ($s['mail_encryption'] ?? 'tls') === 'null' ? null : ($s['mail_encryption'] ?? 'tls'));
                Config::set('mail.from.address', $s['mail_from_address'] ?? config('mail.from.address'));
                Config::set('mail.from.name',    $s['mail_from_name']    ?? config('mail.from.name'));
            }

            // Paystack
            if (!empty($s['paystack_public_key'])) {
                Config::set('services.paystack.public_key',     $s['paystack_public_key']);
                Config::set('services.paystack.secret_key',     $s['paystack_secret_key']     ?? '');
                Config::set('services.paystack.webhook_secret', $s['paystack_webhook_secret'] ?? '');
            }

            // Google OAuth
            if (!empty($s['google_client_id'])) {
                Config::set('services.google.client_id',     $s['google_client_id']);
                Config::set('services.google.client_secret', $s['google_client_secret'] ?? '');
            }
        } catch (\Throwable) {
            // DB not ready (e.g. during migrations) — skip silently
        }
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
