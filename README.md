# Revesta - Laravel ERP Developer Setup Guide

This guide helps new developers set up the existing Revesta application locally. It covers macOS, Linux (with Nginx), and Windows (with WAMP/XAMPP). Laravel is already hosted in a Git repository and should be cloned.

---

## Requirements

- PHP 8.3
- Composer (latest)
- MariaDB (10.5+ or compatible MySQL)
- Nginx (for Linux/macOS) or Apache (for WAMP/XAMPP)
- Node.js & npm (optional, not required for now)

---

## 1. Clone the Repository

```bash
git clone <repository-url> revesta
cd revesta
```

---

## 2. Set File Permissions (Linux/macOS only)

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

On macOS (if using Valet or similar):
```bash
chmod -R 775 storage bootstrap/cache
```

On Windows (WAMP/XAMPP), make sure `storage` and `bootstrap/cache` folders are writable.

---

## 3. Add `revesta.local` to the Hosts File

### Linux/macOS:
```bash
sudo nano /etc/hosts
```
Add:
```
127.0.0.1 revesta.local
```

### Windows:
Open Notepad as Administrator and edit:
```
C:\Windows\System32\drivers\etc\hosts
```
Add this line:
```
127.0.0.1 revesta.local
```

---

## 4. Nginx Configuration (Linux/macOS)

Create a site config file:
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

Enable the site and reload:
```bash
sudo ln -s /etc/nginx/sites-available/revesta /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl restart nginx
```

---

## 5. Apache (Windows with WAMP/XAMPP)

Make sure Apache virtual hosts are enabled, and configure like:

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

Restart Apache from the WAMP/XAMPP control panel.

---

## 6. Create Database

Create an empty database called:
```
revesta_db
```

No other configuration is needed.

---

## 7. Configure Environment File

Copy the default environment file:
```bash
cp .env.example .env
```

Edit the `.env` and modify the database section:
```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=revesta_db
DB_USERNAME=root
DB_PASSWORD=root
```

---

## 8. Generate Application Key

```bash
php artisan key:generate
```

---

## 9. Run Migrations

```bash
php artisan migrate
```

---

## 10. Access the Application

Visit:
```
http://revesta.local
```

Laravel and the ERP app should now be working locally.

