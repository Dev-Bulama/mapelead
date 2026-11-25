<?php
namespace App\Services\CMS;

use App\Models\EmailTemplate;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

class EmailTemplateService
{
    private function branding(): array
    {
        try {
            $s = SiteSetting::getGroup('general');
            return [
                'app_name'      => $s['site_name']    ?? config('app.name'),
                'logo_url'      => !empty($s['site_logo']) ? asset('storage/' . $s['site_logo']) : null,
                'support_email' => $s['contact_email'] ?? config('mail.from.address'),
            ];
        } catch (\Throwable) {
            return ['app_name' => config('app.name'), 'logo_url' => null, 'support_email' => null];
        }
    }

    private function wrap(string $bodyHtml, string $subject = ''): string
    {
        $brand = $this->branding();
        return view('emails.base', array_merge($brand, [
            'subject'      => $subject,
            'body_content' => $bodyHtml,
        ]))->render();
    }

    public function send(string $slug, User $recipient, array $variables = []): bool
    {
        $template = EmailTemplate::findBySlug($slug);
        if (!$template) return false;

        $subject = $template->renderSubject($variables);
        $body    = $template->render($variables);
        $html    = $this->wrap($body, $subject);

        try {
            Mail::html($html, function (Message $message) use ($recipient, $subject) {
                $message->to($recipient->email, $recipient->full_name)->subject($subject);
            });

            $template->update(['last_used_at' => now()]);
            return true;
        } catch (\Exception $e) {
            Log::error("EmailTemplateService: failed to send [{$slug}] to {$recipient->email}: " . $e->getMessage());
            return false;
        }
    }

    public function preview(string $slug, array $variables = []): ?array
    {
        $template = EmailTemplate::findBySlug($slug);
        if (!$template) return null;

        $subject = $template->renderSubject($variables);
        $body    = $template->render($variables);

        return [
            'subject' => $subject,
            'body'    => $this->wrap($body, $subject),
        ];
    }
}
