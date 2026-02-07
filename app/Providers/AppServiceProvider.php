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
    if (! Schema::hasTable('scopes')) {
        return;
    }

    // jetzt erst sichere Abfragen, z.B. falls vorher Scope::orderBy(...) stand:
    $scopes = Scope::orderBy('name')->get();
    view()->share('scopes', $scopes);

    // ... weitere initialisierungen, die DB benötigen ...
}

}
