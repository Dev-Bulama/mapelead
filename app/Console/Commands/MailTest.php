<?php

namespace App\Console\Commands;

use App\Models\SiteSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class MailTest extends Command
{
    protected $signature   = 'mail:test {email : Recipient email address}';
    protected $description = 'Send a test email using the current SMTP configuration';

    public function handle(): int
    {
        $to = $this->argument('email');

        $this->info('Loading mail config from database...');
        $s = SiteSetting::getGroup('integrations');

        if (!empty($s['mail_mailer'])) {
            Config::set('mail.default', $s['mail_mailer']);
            Config::set('mail.mailers.smtp.host',    $s['mail_host']     ?? config('mail.mailers.smtp.host'));
            Config::set('mail.mailers.smtp.port',    (int) ($s['mail_port'] ?? config('mail.mailers.smtp.port')));
            Config::set('mail.mailers.smtp.username', $s['mail_username'] ?? config('mail.mailers.smtp.username'));
            Config::set('mail.mailers.smtp.password', $s['mail_password'] ?? config('mail.mailers.smtp.password'));
            $scheme = ($s['mail_encryption'] ?? 'tls') === 'null' ? null : ($s['mail_encryption'] ?? 'tls');
            Config::set('mail.mailers.smtp.scheme', $scheme);
            Config::set('mail.from.address', $s['mail_from_address'] ?? config('mail.from.address'));
            Config::set('mail.from.name',    $s['mail_from_name']    ?? config('mail.from.name'));

            $this->line("  Host:       " . config('mail.mailers.smtp.host'));
            $this->line("  Port:       " . config('mail.mailers.smtp.port'));
            $this->line("  Scheme:     " . (config('mail.mailers.smtp.scheme') ?? '(null/auto)'));
            $this->line("  Username:   " . config('mail.mailers.smtp.username'));
            $this->line("  From:       " . config('mail.from.address'));
        } else {
            $this->warn('No SMTP settings found in database — using .env defaults.');
            $this->line("  Host:   " . config('mail.mailers.smtp.host'));
            $this->line("  Port:   " . config('mail.mailers.smtp.port'));
        }

        try {
            app('mail.manager')->forgetMailers();
        } catch (\Throwable) {}

        $this->newLine();
        $this->info("Sending test email to {$to}...");

        try {
            Mail::raw(
                "This is a test email from Mapelead.\n\nIf you receive this, your SMTP configuration is working correctly.\n\nSent at: " . now()->toDateTimeString(),
                function ($msg) use ($to) {
                    $msg->to($to)->subject('[Mapelead] SMTP Test — ' . now()->format('Y-m-d H:i:s'));
                }
            );

            $this->info('Test email sent successfully!');
            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to send test email:');
            $this->error($e->getMessage());
            return self::FAILURE;
        }
    }
}
