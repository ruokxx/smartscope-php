<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Setting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class CustomVerifyEmail extends VerifyEmail
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $verificationUrl);
        }

        // Fetch settings
        $settings = Setting::all()->pluck('value', 'key');
        $locale = app()->getLocale();

        // Defaults
        $defaultSubjectEn = 'Verify Email Address';
        $defaultBodyEn = "Please click the button below to verify your email address.\n\n{action_url}\n\nIf you did not create an account, no further action is required.";

        $defaultSubjectDe = 'E-Mail-Adresse bestätigen';
        $defaultBodyDe = "Bitte klicken Sie auf die Schaltfläche unten, um Ihre E-Mail-Adresse zu bestätigen.\n\n{action_url}\n\nWenn Sie kein Konto erstellt haben, ist keine weitere Aktion erforderlich.";

        if ($locale === 'de') {
            $subject = $settings['email_verify_subject_de'] ?? $defaultSubjectDe;
            $body = $settings['email_verify_body_de'] ?? $defaultBodyDe;
        }
        else {
            $subject = $settings['email_verify_subject_en'] ?? $defaultSubjectEn;
            $body = $settings['email_verify_body_en'] ?? $defaultBodyEn;
        }

        // Replace placeholders
        $body = str_replace('{username}', $notifiable->name, $body);
        $body = str_replace('{action_url}', $verificationUrl, $body);

        $mail = (new MailMessage)
            ->subject($subject);

        $lines = explode("\n", $body);
        foreach ($lines as $line) {
            if (trim($line))
                $mail->line(trim($line));
        }

        // Always add the button for good measure
        $mail->action('Verify Email Address', $verificationUrl);

        return $mail;
    }
}
