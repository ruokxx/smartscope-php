# 🌙 Smartscope Bilder Vergleich (Picture Compare)

**Ein Ort zum Teilen, Vergleichen und Entdecken von Astrofotografie.**
*Entwickelt für Smart-Teleskope wie Seestar S50, Dwarf II und Vespera.*

---

## 🚀 Über das Projekt

**Smartscope Bilder Vergleich** ist eine Webanwendung, die es Astrofotografen ermöglicht, ihre Aufnahmen hochzuladen, zu katalogisieren und mit anderen zu vergleichen. Ziel ist es, die Leistung verschiedener Smart-Teleskope unter verschiedenen Bedingungen (Bortle-Skala, Belichtungszeit, Filter) direkt gegenüberzustellen.

### ✨ Features

*   **🔭 Deep Sky Objekt (DSO) Datenbank**: Umfangreicher Katalog an Nebeln, Galaxien und Sternhaufen (Messier, NGC, IC).
*   **🆚 Vergleichsmodus**: Wähle zwei Bilder aus und vergleiche sie Seite an Seite, um Unterschiede in Details und Qualität zu analysieren.
*   **👤 Benutzerprofile & Sammlungen**:
    *   Verfolge deinen Fortschritt (Welche Objekte habe ich schon fotografiert?).
    *   "Captured" vs. "Missing" Status für jedes Objekt.
    *   Verwalte deine eigene Ausrüstung (Teleskope).
*   **📱 Responsive Design**: Optimiert für Desktop, Tablet und Smartphone.
*   **🌍 Mehrsprachigkeit**: Vollständig lokalisiert in **Deutsch** (Standard) und **Englisch**.
*   **🎨 Premium UI**: Modernes Dark-Theme (Space-Look) mit Glassmorphism-Effekten und Gradienten.
*   **📰 News & Changelog**: Integriertes System für Neuigkeiten und Updates direkt auf der Startseite.

---

## 🛠️ Technologie-Stack

Dieses Projekt basiert auf modernen Web-Technologien:

*   **Backend**: PHP 8.1+ / [Laravel 10](https://laravel.com)
*   **Datenbank**: MySQL / MariaDB
*   **Frontend**: Blade Templates, Vanilla CSS (CSS Variables), Vanilla JS
*   **Server**: Nginx / Apache

---

## 📦 Installation (Lokal)

Du möchtest das Projekt lokal ausführen? Folge diesen Schritten:

### Voraussetzungen
*   PHP 8.1 oder höher
*   Composer
*   Node.js & npm
*   MySQL Datenbank

### Schritte

1.  **Repository klonen**
    ```bash
    git clone https://github.com/ruokxx/smartscope-php.git
    cd smartscope-php
    ```

2.  **Abhängigkeiten installieren**
    ```bash
    composer install
    npm install
    ```

3.  **Umgebungsvariablen konfigurieren**
    *   Kopiere `.env.example` zu `.env`:
        ```bash
        cp .env.example .env
        ```
    *   Öffne `.env` und trage deine Datenbank-Zugangsdaten ein (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).

4.  **App-Key generieren**
    ```bash
    php artisan key:generate
    ```

5.  **Datenbank migrieren & seeden**
    ```bash
    php artisan migrate --seed
    ```
    *Dies legt die Tabellen an und füllt sie mit Testdaten und dem DSO-Katalog.*

6.  **Storage Link setzen**
    ```bash
    php artisan storage:link
    ```

7.  **Server starten**
    ```bash
    npm run dev
    php artisan serve
    ```

Die Anwendung ist nun unter `http://localhost:8000` erreichbar.

---

## 🖼️ Vorschau

*(Hier können Screenshots der Anwendung eingefügt werden)*

---

## 📝 Lizenz

Dieses Projekt ist Open-Source-Software lizenziert unter der [MIT license](https://opensource.org/licenses/MIT).

---

© 2026 Sebastian Thielke
