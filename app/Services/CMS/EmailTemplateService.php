<?php
namespace App\Services\CMS;

use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

class EmailTemplateService
{
    public function send(string $slug, User $recipient, array $variables = []): bool
    {
        $template = EmailTemplate::findBySlug($slug);
        if (!$template) return false;

        $subject = $template->renderSubject($variables);
        $body    = $template->render($variables);

        try {
            Mail::html($body, function (Message $message) use ($recipient, $subject) {
                $message->to($recipient->email, $recipient->full_name)->subject($subject);
            });

            $template->update(['last_used_at' => now()]);
            return true;
        } catch (\Exception) {
            return false;
        }
    }

    public function preview(string $slug, array $variables = []): ?array
    {
        $template = EmailTemplate::findBySlug($slug);
        if (!$template) return null;

        return [
            'subject' => $template->renderSubject($variables),
            'body'    => $template->render($variables),
        ];
    }
}
