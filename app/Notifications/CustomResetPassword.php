<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Setting;

class CustomResetPassword extends ResetPassword
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        // Fetch settings
        $settings = Setting::all()->pluck('value', 'key');

        $locale = app()->getLocale(); // 'en', 'de', etc.

        // Define defaults
        $defaultSubjectEn = 'Reset Password Notification';
        $defaultBodyEn = "You are receiving this email because we received a password reset request for your account.\n\n{action_url}\n\nThis password reset link will expire in :count minutes.\n\nIf you did not request a password reset, no further action is required.";

        $defaultSubjectDe = 'Passwort zurücksetzen';
        $defaultBodyDe = "Sie erhalten diese E-Mail, weil wir eine Anfrage zum Zurücksetzen des Passworts für Ihr Konto erhalten haben.\n\n{action_url}\n\nDieser Link zum Zurücksetzen des Passworts läuft in :count Minuten ab.\n\nWenn Sie kein Zurücksetzen des Passworts angefordert haben, ist keine weitere Aktion erforderlich.";

        // Try to get localized settings, fallback to EN
        if ($locale === 'de') {
            $subject = $settings['email_reset_subject_de'] ?? $defaultSubjectDe;
            $body = $settings['email_reset_body_de'] ?? $defaultBodyDe;
        }
        else {
            $subject = $settings['email_reset_subject_en'] ?? $defaultSubjectEn;
            $body = $settings['email_reset_body_en'] ?? $defaultBodyEn;
        }

        // Replace placeholders
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $count = config('auth.passwords.' . config('auth.defaults.passwords') . '.expire');

        // Replace basic placeholders
        $body = str_replace('{username}', $notifiable->name, $body);
        $body = str_replace(':count', $count, $body);

        // We handle {action_url} specifically to create a button if possible, but for raw text customization 
        // usually we want to control where the button goes. 
        // Laravel's MailMessage 'action' method adds a button.
        // If the user puts {action_url} in the text, we might want to just put the URL string there?
        // Or we can try to split the body around the button?

        // Simpler approach for now: 
        // If {action_url} is present in body, replace it with the URL string.
        // AND still add the standard button at the bottom if usage isn't detected or just always add it?
        // Let's stick to standard layout: Intro lines -> Action Button -> Outro lines.
        // But the user asked for "template text".

        // Let's treat the body as the "Intro" lines.
        // And maybe add an optional "Footer/Outro"?
        // Or just let the body be the whole thing and if they want a link they use {action_url} as text.

        // Better UX: 
        // Replicate Laravel's structure: 
        // Subject
        // Body (Lines)
        // Action Button
        // Outro (Lines) - handled by Lang usually.

        // Let's construct the MailMessage dynamically.
        $mail = (new MailMessage)
            ->subject($subject);

        // Check if body contains {action_url}
        if (strpos($body, '{action_url}') !== false) {
            // If user explicitly put the URL placeholder, replace it and don't force a button? 
            // Or maybe they want the button AND the link?
            // Let's just replacing {action_url} with the actual URL in the text.
            $body = str_replace('{action_url}', $url, $body);

            // Split by newlines to make paragraphs
            $lines = explode("\n", $body);
            foreach ($lines as $line) {
                if (trim($line))
                    $mail->line(trim($line));
            }

            // Still add button? Maybe safer to always add the button unless we have a sophisticated template engine.
            $mail->action('Reset Password', $url);
        }
        else {
            // Standard behavior override
            $lines = explode("\n", $body);
            foreach ($lines as $line) {
                if (trim($line))
                    $mail->line(trim($line));
            }
            $mail->action('Reset Password', $url);
        }

        return $mail;
    }
}
