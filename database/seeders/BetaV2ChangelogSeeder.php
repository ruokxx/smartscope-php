<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Changelog;

class BetaV2ChangelogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Changelog::create([
            'title' => 'Beta V2 - Community & Design Update',
            'version' => 'Beta V2',
            'published_at' => now(),
            'body' => "### **Neuer Name & Design**
*   Das Projekt heißt nun **Smart Teleskop Astrofoto**.
*   Neues, animiertes Header-Design mit Shimmer-Effekt.
*   Verbesserter Footer mit Server-Statistiken.

### **Community Features**
*   **Gruppen:** Erstelle und verwalte eigene Gruppen.
*   **Feed:** Neuer Activity-Feed für Community-Beiträge.
*   **Forum:** Integriertes Forum für Diskussionen.

### **Sonstiges**
*   Erweiterte Admin-Einstellungen (SMTP, Texte).
*   Login-Zwang für Gäste bei Community-Features.
*   Mobile Optimierungen und Bugfixes.",
        ]);
    }
}
