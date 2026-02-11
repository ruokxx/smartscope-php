<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\News;

class BetaV2NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        News::create([
            'title' => 'Smart Teleskop Astrofoto Beta V2 ist da!',
            'published' => true,
            'body' => "Hallo Astro-Freunde,

wir freuen uns, euch das große **Beta V2 Update** präsentieren zu können!
Die Seite wurde komplett überarbeitet und bietet nun viele neue Funktionen:

*   **Neues Design:** Ein moderner Look mit animierten Elementen.
*   **Gruppen & Community:** Tauscht euch in eigenen Gruppen aus.
*   **Forum:** Diskutiert über Technik, Bildbearbeitung und mehr.
*   **Bessere Performance:** Die Seite lädt nun schneller und ist für Handys optimiert.

Wir hoffen, euch gefällt das Update!
Euer Smart Teleskop Team",
        ]);
    }
}
