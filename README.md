# 🌙 Smartscope Bilder Vergleich (Picture Compare)

**Ein Ort zum Teilen, Vergleichen und Entdecken von Astrofotografie.**
*Entwickelt für Smart-Teleskope wie Seestar S50, Dwarf II.*

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

## 📦 Installation

### 📋 Voraussetzungen

Für den Betrieb der Anwendung werden folgende Komponenten benötigt:

*   **PHP**: Version 8.1 oder höher
*   **Datenbank**: MySQL oder MariaDB
*   **Webserver**: Nginx oder Apache
*   **Tools**: [Composer](https://getcomposer.org/), [Node.js](https://nodejs.org/) & npm

### 💻 Installation (Lokal)

#### 🪟 Windows
Wir empfehlen die Nutzung von **Laragon** oder **XAMPP**.
1.  Stelle sicher, dass PHP 8.1+ und MySQL laufen.
2.  Installiere Composer und Node.js für Windows.
3.  Öffne eine PowerShell oder Git Bash im Projektordner und folge den allgemeinen Schritten unten.

#### 🐧 Linux (Ubuntu/Debian)
Installiere die benötigten Pakete:
```bash
sudo apt update
sudo apt install -y php8.1 php8.1-cli php8.1-fpm php8.1-mysql php8.1-curl php8.1-gd php8.1-mbstring php8.1-xml php8.1-zip unzip
sudo apt install -y mysql-server nginx composer nodejs npm
```

### 🚀 Allgemeine Einrichtungsschritte

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

3.  **Konfiguration**
    *   Kopiere die `.env.example`:
        ```bash
        cp .env.example .env
        ```
    *   Bearbeite die `.env` Datei und trage deine Datenbank-Infos ein:
        ```ini
        DB_DATABASE=smartscope_db
        DB_USERNAME=root
        DB_PASSWORD=
        ```

4.  **App-Key generieren**
    ```bash
    php artisan key:generate
    ```

5.  **Datenbank einrichten**
    ```bash
    # Erstellt Tabellen und füllt sie mit DSO-Daten & Test-Usern
    php artisan migrate --seed
    ```

6.  **Storage verlinken**
    ```bash
    php artisan storage:link
    ```

7.  **Starten**
    *   **Backend & Server**: `php artisan serve`
    *   **Frontend Assets** (in neuem Terminal): `npm run dev`

Die Seite ist nun unter [http://localhost:8000](http://localhost:8000) erreichbar.

---

## 🔑 Admin Account & Verwaltung

Das System verfügt über ein Admin-Panel für die Verwaltung von Benutzern und News/Changelogs.

### Standard Admin-Account
Wenn du `php artisan migrate --seed` ausgeführt hast, wird automatisch ein Admin-Benutzer angelegt:

*   **E-Mail**: `admin@example.com`
*   **Passwort**: `adminpassword`

### Admin manuell erstellen
Du kannst jedem existierenden Benutzer Admin-Rechte über die Konsole (Tinker) geben:

1.  Öffne die Tinker-Konsole:
    ```bash
    php artisan tinker
    ```

2.  Führe folgenden PHP-Code aus (ersetze die ID oder E-Mail entsprechend):
    ```php
    $user = \App\Models\User::where('email', 'deine.email@example.com')->first();
    $user->is_admin = true;
    $user->save();
    exit;
    ```

---

## 🖼️ Vorschau

*(Hier können Screenshots der Anwendung eingefügt werden)*

---

## 📝 Lizenz

Dieses Projekt ist Open-Source-Software lizenziert unter der [MIT license](https://opensource.org/licenses/MIT).

---

© 2026 Sebastian Thielke
