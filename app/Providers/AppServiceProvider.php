<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View; // <-- hier richtig importieren
use Illuminate\Support\Facades\Schema;
use App\Models\Scope;


use Illuminate\Pagination\Paginator; // Use Paginator

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
    //
    }
    public function boot()
    {
        Paginator::useBootstrapFive(); // Enable Bootstrap 5 Pagination

        // Allow running in console to ensure queues get the config too
        // if ($this->app->runningInConsole()) {
        //     return;
        // }


        // Verhindere DB‑Abfragen, wenn die Tabelle noch nicht existiert
        if (!Schema::hasTable('scopes') || !Schema::hasTable('settings')) {
            return;
        }

        // Load settings from DB
        $settings = \App\Models\Setting::all()->pluck('value', 'key');

        // Force HTTPS if enabled
        if (isset($settings['ssl_enabled']) && $settings['ssl_enabled']) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

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
        // Configure External Storage dynamically
        if (isset($settings['storage_driver']) && in_array($settings['storage_driver'], ['s3', 'ftp', 'sftp'])) {
            $driver = $settings['storage_driver'];

            if ($driver === 's3') {
                config([
                    'filesystems.disks.s3.key' => $settings['s3_key'] ?? '',
                    'filesystems.disks.s3.secret' => $settings['s3_secret'] ?? '',
                    'filesystems.disks.s3.region' => $settings['s3_region'] ?? '',
                    'filesystems.disks.s3.bucket' => $settings['s3_bucket'] ?? '',
                    'filesystems.disks.s3.url' => $settings['s3_url'] ?? '',
                    'filesystems.disks.s3.use_path_style_endpoint' => $settings['s3_use_path_style_endpoint'] ?? false,
                ]);
            }
            elseif ($driver === 'ftp') {
                config([
                    'filesystems.disks.ftp' => [
                        'driver' => 'ftp',
                        'host' => $settings['ftp_host'] ?? '',
                        'username' => $settings['ftp_username'] ?? '',
                        'password' => $settings['ftp_password'] ?? '',
                        'root' => $settings['ftp_root'] ?? '',
                    ]
                ]);
            }
        }

        // jetzt erst sichere Abfragen, z.B. falls vorher Scope::orderBy(...) stand:
        $scopes = Scope::orderBy('name')->get();

        // Share stats with all views
        if (Schema::hasTable('users') && Schema::hasTable('images')) {
            $stats = [
                'users_count' => \App\Models\User::count(),
                'images_count' => \App\Models\Image::count(),
            ];

            // Disk Space Calculation (Cached for 1 hour to avoid performance hit)
            // Use Cache facade
            $diskStats = \Illuminate\Support\Facades\Cache::remember('disk_stats', 3600, function () {
                $path = base_path(); // Check disk space of the partition where the app is installed
                $total = disk_total_space($path);
                $free = disk_free_space($path);
                $used = $total - $free;

                return [
                'total_gb' => round($total / 1024 / 1024 / 1024, 2),
                'free_gb' => round($free / 1024 / 1024 / 1024, 2),
                'used_gb' => round($used / 1024 / 1024 / 1024, 2),
                'used_percent' => $total > 0 ? round(($used / $total) * 100, 1) : 0,
                ];
            });

            $stats = array_merge($stats, $diskStats);

            view()->share('global_stats', $stats);
            view()->share('settings', $settings);
        }

        view()->share('scopes', $scopes);

    // ... weitere initialisierungen, die DB benötigen ...
    }
}
