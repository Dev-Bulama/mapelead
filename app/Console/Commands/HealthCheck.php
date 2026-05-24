<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class HealthCheck extends Command
{
    protected $signature   = 'app:health';
    protected $description = 'Check app health — DB, routes, env, storage, migrations';

    public function handle(): int
    {
        $this->info('');
        $this->info('═══════════════════════════════════════');
        $this->info('  Mapelead Health Check');
        $this->info('═══════════════════════════════════════');

        $pass = 0;
        $fail = 0;

        // ── 1. Required .env keys ──────────────────────────────────────────
        $this->line('');
        $this->comment('[ ENV ]');
        $required = [
            'APP_KEY', 'APP_URL', 'DB_CONNECTION',
        ];
        foreach ($required as $key) {
            if (env($key)) {
                $this->line("  ✓ {$key}");
                $pass++;
            } else {
                $this->error("  ✗ {$key} is not set");
                $fail++;
            }
        }

        // Warn about placeholder values
        $placeholders = [
            'PAYSTACK_SECRET_KEY'  => 'your_',
            'MAIL_USERNAME'        => 'your_mailgun',
        ];
        foreach ($placeholders as $key => $placeholder) {
            $val = env($key, '');
            if ($val && str_starts_with($val, $placeholder)) {
                $this->warn("  ⚠ {$key} still has a placeholder value");
            }
        }

        // ── 2. Database connection ─────────────────────────────────────────
        $this->line('');
        $this->comment('[ DATABASE ]');
        try {
            DB::connection()->getPdo();
            $this->line('  ✓ Database connection OK');
            $pass++;
        } catch (\Exception $e) {
            $this->error('  ✗ Database connection FAILED: ' . $e->getMessage());
            $fail++;
        }

        // ── 3. Pending migrations ──────────────────────────────────────────
        $this->comment('[ MIGRATIONS ]');
        try {
            $pending = collect(\Artisan::call('migrate:status', ['--pending' => true]) ? [] : []);
            $output  = \Artisan::output();
            if (str_contains($output, 'No pending migrations')) {
                $this->line('  ✓ No pending migrations');
                $pass++;
            } else {
                $this->warn('  ⚠ Pending migrations detected — run: php artisan migrate --force');
            }
        } catch (\Exception $e) {
            $this->warn('  ⚠ Could not check migrations: ' . $e->getMessage());
        }

        // ── 4. Storage writable ────────────────────────────────────────────
        $this->line('');
        $this->comment('[ STORAGE ]');
        $paths = [storage_path('logs'), storage_path('app'), storage_path('framework/cache'), storage_path('framework/views'), base_path('bootstrap/cache')];
        foreach ($paths as $path) {
            if (is_writable($path)) {
                $this->line('  ✓ ' . str_replace(base_path() . '/', '', $path));
                $pass++;
            } else {
                $this->error('  ✗ NOT writable: ' . str_replace(base_path() . '/', '', $path));
                $fail++;
            }
        }

        // ── 5. Critical route names ────────────────────────────────────────
        $this->line('');
        $this->comment('[ ROUTES ]');
        $criticalRoutes = [
            'login', 'register', 'home',
            'auth.login', 'auth.register', 'auth.logout',
            'student.dashboard', 'admin.dashboard',
            'student.courses', 'student.payments',
        ];
        foreach ($criticalRoutes as $name) {
            if (Route::has($name)) {
                $this->line("  ✓ {$name}");
                $pass++;
            } else {
                $this->error("  ✗ Route [{$name}] not defined");
                $fail++;
            }
        }

        // ── 6. Storage symlink ─────────────────────────────────────────────
        $this->line('');
        $this->comment('[ ASSETS ]');
        $symlink = public_path('storage');
        if (file_exists($symlink) && is_link($symlink)) {
            $this->line('  ✓ storage symlink exists');
            $pass++;
        } else {
            $this->error('  ✗ storage symlink missing — run: php artisan storage:link');
            $fail++;
        }

        // ── Result ─────────────────────────────────────────────────────────
        $this->line('');
        $this->info('═══════════════════════════════════════');
        if ($fail === 0) {
            $this->info("  ✓ All checks passed ({$pass} total)");
            $this->info('═══════════════════════════════════════');
            $this->line('');
            return self::SUCCESS;
        }

        $this->error("  ✗ {$fail} check(s) failed, {$pass} passed");
        $this->info('═══════════════════════════════════════');
        $this->line('');
        return self::FAILURE;
    }
}
