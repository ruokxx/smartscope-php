<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Setting;

class CustomVerifyEmail extends VerifyEmail
{
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);
        $welcomeMessage = Setting::where('key', 'welcome_message')->value('value');

        return (new MailMessage)
            ->subject(\Illuminate\Support\Facades\Lang::get('Verify Email Address'))
            ->line($welcomeMessage ?: \Illuminate\Support\Facades\Lang::get('Please click the button below to verify your email address.'))
            ->action(\Illuminate\Support\Facades\Lang::get('Verify Email Address'), $verificationUrl)
            ->line(\Illuminate\Support\Facades\Lang::get('If you did not create an account, no further action is required.'));
    }
}
