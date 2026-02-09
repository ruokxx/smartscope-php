<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View; // <-- hier richtig importieren
use Illuminate\Support\Facades\Schema;
use App\Models\Scope;


class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
    //
    }
    public function boot()
    {
        // Wenn die App gerade in der Konsole läuft (z.B. beim composer install/migrate), skip ggf.
        if ($this->app->runningInConsole()) {
            return;
        }

        // Verhindere DB‑Abfragen, wenn die Tabelle noch nicht existiert
        if (!Schema::hasTable('scopes') || !Schema::hasTable('settings')) {
            return;
        }

        // Load settings from DB
        $settings = \App\Models\Setting::all()->pluck('value', 'key');

        // Override Mail Config
        if (isset($settings['smtp_enabled']) && $settings['smtp_enabled']) {
            config([
                'mail.default' => 'smtp',
                'mail.mailers.smtp.host' => $settings['mail_host'] ?? config('mail.mailers.smtp.host'),
                'mail.mailers.smtp.port' => $settings['mail_port'] ?? config('mail.mailers.smtp.port'),
                'mail.mailers.smtp.username' => $settings['mail_username'] ?? config('mail.mailers.smtp.username'),
                'mail.mailers.smtp.password' => $settings['mail_password'] ?? config('mail.mailers.smtp.password'),
                'mail.mailers.smtp.encryption' => $settings['mail_encryption'] ?? config('mail.mailers.smtp.encryption'),
                'mail.from.address' => $settings['mail_from_address'] ?? config('mail.from.address'),
                'mail.from.name' => $settings['mail_from_name'] ?? config('mail.from.name'),
            ]);
        }
        else {
            // Disable sending if not enabled (use array driver which discards emails, or log)
            config(['mail.default' => 'array']);
        }

        // jetzt erst sichere Abfragen, z.B. falls vorher Scope::orderBy(...) stand:
        $scopes = Scope::orderBy('name')->get();
        view()->share('scopes', $scopes);

    // ... weitere initialisierungen, die DB benötigen ...
    }
}
