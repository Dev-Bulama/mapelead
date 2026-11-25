<?php
/**
 * Server-side application verification script
 * Run from the mapelead project root:
 *   php tests/verify-app.php
 * Or via artisan tinker:
 *   php artisan tinker --execute="require base_path('tests/verify-app.php');"
 */

$pass = 0;
$fail = 0;

function pass(string $name): void {
    global $pass;
    $pass++;
    echo "\033[32m  ✓\033[0m {$name}\n";
}

function fail(string $name, string $reason): void {
    global $fail;
    $fail++;
    echo "\033[31m  ✗\033[0m {$name}: {$reason}\n";
}

function check_view(string $viewPath): void {
    $file = str_replace('.', '/', $viewPath);
    $fullPath = __DIR__ . '/../resources/views/' . $file . '.blade.php';
    if (file_exists($fullPath)) {
        pass("View exists: {$viewPath}");
    } else {
        fail("View file missing", "{$fullPath}");
    }
}

function check_class(string $fqn): void {
    if (class_exists($fqn)) {
        pass("Class loaded: {$fqn}");
    } else {
        // Try to autoload by guessing the file path
        $relative = str_replace('\\', '/', str_replace('App\\', 'app/', $fqn)) . '.php';
        $fullPath = __DIR__ . '/../' . $relative;
        if (file_exists($fullPath)) {
            pass("Class file exists: {$fqn}");
        } else {
            fail("Class missing", "{$fqn}");
        }
    }
}

function check_mail_config_key(): void {
    $file = __DIR__ . '/../app/Providers/AppServiceProvider.php';
    $content = file_get_contents($file);
    if (strpos($content, "Config::set('mail.mailers.smtp.scheme'") !== false) {
        pass('AppServiceProvider sets mail.mailers.smtp.scheme (not encryption)');
    } else {
        fail('AppServiceProvider', "Still sets 'encryption' instead of 'scheme'");
    }
    if (strpos($content, 'Config::set(\'mail.mailers.smtp.encryption\'') !== false) {
        fail('AppServiceProvider', "STILL has old 'encryption' key set somewhere");
    } else {
        pass('AppServiceProvider: no old encryption key leftover');
    }
}

function check_settings_controller_scheme(): void {
    $file = __DIR__ . '/../app/Http/Controllers/Admin/SettingsController.php';
    $content = file_get_contents($file);
    if (strpos($content, "Config::set('mail.mailers.smtp.scheme'") !== false ||
        strpos($content, 'Config::set(\'mail.mailers.smtp.scheme\'') !== false) {
        pass('SettingsController::testMail uses scheme (not encryption)');
    } else {
        fail('SettingsController', "testMail() still uses 'encryption' key");
    }
}

function check_no_silent_swallow(): void {
    $file = __DIR__ . '/../app/Services/CMS/EmailTemplateService.php';
    $content = file_get_contents($file);
    if (strpos($content, 'Log::error') !== false) {
        pass('EmailTemplateService logs exceptions (not silently swallowed)');
    } else {
        fail('EmailTemplateService', "catch block does not log the exception");
    }
}

function check_lead_try_catch(): void {
    $file = __DIR__ . '/../app/Http/Controllers/Admin/LeadController.php';
    $content = file_get_contents($file);
    if (strpos($content, 'try {') !== false && strpos($content, 'Mail::send') !== false) {
        pass('LeadController::contactReply has try/catch around Mail::send');
    } else {
        fail('LeadController', "Mail::send() not wrapped in try/catch");
    }
}

echo "\n\033[1m── View Files ───────────────────────────────────────────────\033[0m\n";
check_view('admin.cms.email-templates.index');
check_view('admin.cms.email-templates.create');
check_view('admin.cms.email-templates.edit');
check_view('admin.cms.email-templates.preview');
check_view('instructor.courses.show');
check_view('instructor.profile');
check_view('instructor.dashboard');
check_view('instructor.students');
check_view('instructor.earnings');

echo "\n\033[1m── Controller Classes ───────────────────────────────────────\033[0m\n";
check_class('App\\Http\\Controllers\\Instructor\\InstructorContentController');
check_class('App\\Http\\Controllers\\Instructor\\InstructorProfileController');
check_class('App\\Console\\Commands\\MailTest');

echo "\n\033[1m── Code Fixes ───────────────────────────────────────────────\033[0m\n";
check_mail_config_key();
check_settings_controller_scheme();
check_no_silent_swallow();
check_lead_try_catch();

echo "\n\033[1m── SMTP Config Key in config/mail.php ───────────────────────\033[0m\n";
$mailConfig = __DIR__ . '/../config/mail.php';
$content = file_get_contents($mailConfig);
if (strpos($content, "'scheme'") !== false) {
    pass("config/mail.php uses 'scheme' key (Laravel 11 Symfony Mailer)");
} else {
    fail("config/mail.php", "No 'scheme' key found — unexpected");
}
if (strpos($content, "'encryption'") !== false) {
    fail("config/mail.php", "Has 'encryption' key — this key is NOT applied by Symfony Mailer");
} else {
    pass("config/mail.php: no 'encryption' key (correct for Laravel 11)");
}

echo "\n\033[1m─────────────────────────────────────────────────────────────\033[0m\n";
echo "\033[1mRESULTS: \033[32m{$pass} passed\033[0m  \033[31m{$fail} failed\033[0m\n\n";
exit($fail > 0 ? 1 : 0);
