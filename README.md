smartscope-php

Laravel Starter für Smartscope Catalog — lokales Development & VPS‑Deployment Anleitungen.
Inhalt

    Kurze Anleitung für lokales Entwickeln (Dev‑Server)
    Schritte zum Deployen auf einem Ubuntu VPS (Nginx, PHP‑FPM, MySQL)
    Demo‑Account

Schnellstart (lokal)

Voraussetzungen

    PHP 8.1+ (CLI)
    Composer
    MySQL / MariaDB
    Node.js & npm

Schritte im Projekt‑Root:

    PHP‑Abhängigkeiten installieren

text

composer install

    Environment Datei kopieren und anpassen

text

cp .env.example .env
# .env öffnen und DB_*, APP_URL, MAIL etc. setzen

    App‑Key erzeugen

text

php artisan key:generate

    Frontend (optional)

text

npm install
npm run dev

    Datenbank erstellen (MySQL) — dann Migrationen & Seeder ausführen

text

php artisan migrate --seed

    Storage Link für öffentliche Uploads

text

php artisan storage:link

    Dev‑Server starten

text

php artisan serve --host=127.0.0.1 --port=8000
# Seite öffnen: http://localhost:8000

Nützliche Befehle:

    DB neu aufsetzen (Entw.): php artisan migrate:fresh --seed
    Storage Link erneuern: php artisan storage:link
    Assets bauen (Prod): npm run build

Demo‑Account:

    E‑Mail: demo@example.com
    Passwort: password

VPS Deployment (Ubuntu Beispiel)

Kurzanleitung für einen frischen Ubuntu‑Server (angepasst auf deine Bedürfnisse).

    System aktualisieren

bash

sudo apt update && sudo apt upgrade -y

    Benötigte Pakete installieren (PHP, MySQL, Nginx, Composer)

bash

# PHP + Extensions (Beispiel für PHP 8.1)
sudo apt install -y php8.1 php8.1-fpm php8.1-mbstring php8.1-xml php8.1-bcmath php8.1-json php8.1-zip php8.1-gd php8.1-curl php8.1-mysql php8.1-cli php8.1-intl php8.1-opcache unzip git curl

# Composer (global)
php -r "copy('https://getcomposer.org/installer','composer-setup.php');"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
php -r "unlink('composer-setup.php')"

# MySQL Server
sudo apt install -y mysql-server
sudo mysql_secure_installation

    Datenbank & Benutzer anlegen

sql

# in mysql shell:
CREATE DATABASE smartscope_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'smartuser'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON smartscope_db.* TO 'smartuser'@'localhost';
FLUSH PRIVILEGES;
EXIT;

    Projekt deployen

bash

cd /var/www
sudo git clone https://github.com/ruokxx/smartscope-php.git
cd smartscope-php
# set owner für deploy user / www-data
sudo chown -R $USER:www-data .
composer install --no-dev --optimize-autoloader
cp .env.example .env
# .env anpassen: APP_URL, DB_* etc.
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm install
npm run build   # optional für Prod assets

    Berechtigungen

bash

sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

    Nginx Konfiguration (Beispiel /etc/nginx/sites-available/smartscope)

text

server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;

    root /var/www/smartscope-php/public;
    index index.php;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }
}

Enable & reload:
bash

sudo ln -s /etc/nginx/sites-available/smartscope /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx

    HTTPS (Let's Encrypt / Certbot)

bash

sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

    Supervisor (optional) — Queue Workers

bash

sudo apt install -y supervisor
# lege Supervisor conf an für Laravel Queue falls benötigt
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*

    Logs prüfen

bash

tail -f /var/www/smartscope-php/storage/logs/laravel.log

Hinweise / Tipps

    .env niemals in Git committen.
    Backups: Sichern der MySQL‑Datenbank und des storage/app/public/uploads Ordners.
    Für große Zahlen an Uploads empfiehlt sich ein S3‑Storage (externe Files).
    Für automatischen Deploys nutze GitHub Actions, Forge oder ein simples Pull‑Script.








<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
