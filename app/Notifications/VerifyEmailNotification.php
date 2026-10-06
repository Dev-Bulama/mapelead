<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends VerifyEmail
{
    protected function buildMailMessage($url): MailMessage
    {
        $appName = config('app.name', 'MapeLead');

        return (new MailMessage)
            ->subject('Verify Your ' . $appName . ' Email Address')
            ->greeting('Welcome to ' . $appName . '!')
            ->line('Please click the button below to verify your email address and activate your account.')
            ->action('Verify Email Address', $url)
            ->line('This verification link expires in ' . config('auth.verification.expire', 60) . ' minutes.')
            ->line('If you did not create an account, no further action is required.')
            ->salutation('The ' . $appName . ' Team');
    }
}
