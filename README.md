# Revesta - Laravel ERP Developer Setup Guide

This guide helps new developers set up the existing Revesta application locally. It covers macOS, Linux (with Nginx), and Windows (with WAMP/XAMPP).

Revesta is a Laravel-based ERP and dashboard tool designed to help users simulate and manage renovation and housing aid requests. It includes an API and is intended to work in combination with a browser extension. The application is responsive and supports desktop and mobile use. It also includes Progressive Web App (PWA) support.

---

## Requirements

* PHP 8.3
* Composer (latest)
* MariaDB (10.5+) or compatible MySQL
* Nginx (Linux/macOS) or Apache (WAMP/XAMPP)
* Node.js & npm (required for front-end assets)
* Git
* Laravel CLI

---

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/Alanarex/revesta.git revesta
cd revesta
```

### 2. Set File Permissions (Linux/macOS only)

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

On macOS (if using Valet or similar):

```bash
chmod -R 775 storage bootstrap/cache
```

On Windows (WAMP/XAMPP), make sure `storage` and `bootstrap/cache` folders are writable.

### 3. Add `revesta.local` to the Hosts File

**Linux/macOS:**

```bash
sudo nano /etc/hosts
```

Add:

```plaintext
127.0.0.1 revesta.local
```

**Windows:**
Edit:

```plaintext
C:\Windows\System32\drivers\etc\hosts
```

Add:

```plaintext
127.0.0.1 revesta.local
```

### 4. Nginx Configuration (Linux/macOS)

```nginx
server {
    listen 80;
    server_name revesta.local;
    root /path/to/revesta/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

Enable the site and restart Nginx:

```bash
sudo ln -s /etc/nginx/sites-available/revesta /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl restart nginx
```

### 5. Apache (Windows with WAMP/XAMPP)

```apache
<VirtualHost *:80>
    DocumentRoot "C:/wamp64/www/revesta/public"
    ServerName revesta.local

    <Directory "C:/wamp64/www/revesta/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Restart Apache via WAMP/XAMPP control panel.

### 6. Create Database

Create a database named:

```plaintext
revesta_db
```

### 7. Configure Environment File

```bash
cp .env.example .env
```

Update database credentials in `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=revesta_db
DB_USERNAME=root
DB_PASSWORD=root
```

### 8. Generate Application Key

```bash
php artisan key:generate
```

### 9. Run Migrations and Seeders

```bash
php artisan migrate
php artisan db:seed
```

### 10. Access the Application

Open your browser and go to:

```plaintext
http://revesta.local
```

---

## Author

**Alaa Khalil**
Full Stack Developer – MBA MyDigitalSchool 2024–2026
Email: [alaa.khalil.dev@gmail.com](mailto:alaa.khalil.dev@gmail.com)
GitHub: [Alanarex/revesta](https://github.com/Alanarex/revesta)

---

## License

This project was originally developed as part of the academic program "My Digital Start-up" at MyDigitalSchool. While it is primarily intended for educational and non-commercial purposes, the authors reserve the right to use it for commercial purposes within their own team.
**All rights reserved © 2025**

---

## Last Updated

**June 18, 2025**

> **Note:** This README will be continuously updated as the project evolves.
