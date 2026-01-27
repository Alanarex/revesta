# Revesta - Laravel ERP Developer Setup Guide

This guide helps new developers set up the existing Revesta application locally. It covers macOS, Linux (with Nginx), and Windows (with WAMP/XAMPP).

Revesta is a Laravel-based ERP and dashboard tool designed to help users simulate and manage renovation and housing aid requests. It includes an API and is intended to work in combination with a browser extension. The application is responsive and supports desktop and mobile use. It also includes Progressive Web App (PWA) support.

---

## Requirements

* PHP 8.2
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

It's also required to create the test database used by the test suite. Create both with:

```bash
mysql -u root -proot -e "CREATE DATABASE IF NOT EXISTS revesta_db;"
mysql -u root -proot -e "CREATE DATABASE IF NOT EXISTS revesta_testing;"
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

### 9.a Laravel Passport (API authentication)

This project uses Laravel Passport for API authentication. The commands and notes below reflect how Passport is used in this repository — keys remain in `storage/` by default.

Basic install & keys

```bash
# Install Passport (if not already required via composer)
composer require laravel/passport

# Create encryption keys and default clients (interactive)
php artisan passport:install

# Force-generate RSA keys (useful to refresh keys non-interactively)
php artisan passport:keys --force

# Apply migrations (run after `passport:install` in this project)
php artisan migrate

# Ensure storage key file permissions (web user should be able to read keys)
chown www-data:www-data storage/oauth-*.key || true
chmod 600 storage/oauth-private.key || true
chmod 640 storage/oauth-public.key || true
```

Client creation

```bash
# Create a password-grant client (the command will prompt for a name and provider)
php artisan passport:client --password
```

You do not need to pre-specify the name/provider in the command; the interactive prompt will ask and the created client will work for the password grant flow.

Useful maintenance & inspection commands

```bash
# List oauth clients (quick DB check)
php artisan tinker --execute="DB::table('oauth_clients')->get()"

# Re-run migrations from scratch (DESTROYS data) and reseed
php artisan migrate:fresh --seed

# Clear all caches and compiled files during debugging
php artisan optimize:clear

# Regenerate Composer autoload files
composer dump-autoload
```

Testing OAuth token issuance (example using Password Grant)

```bash
# Replace CLIENT_ID and CLIENT_SECRET with the client credentials from oauth_clients
curl -u "CLIENT_ID:CLIENT_SECRET" -X POST "http://revesta.local/oauth/token" \
    -d "grant_type=password&username=admin@gmail.com&password=password&scope=*"
```

Debugging & troubleshooting notes

- Keys and permissions:
    - Keys are stored in `storage/oauth-private.key` and `storage/oauth-public.key` by default in this project. Ensure these files exist and the web user (for example `www-data`) can read them.
    - If you encounter token signature or EncryptionException errors, re-run `php artisan passport:keys --force` and ensure the permissions above are set. Run `php artisan optimize:clear` after key or config changes.

- Database and clients:
    - Check the `oauth_clients`, `oauth_access_tokens`, and `oauth_refresh_tokens` tables to verify clients and tokens. Use `php artisan tinker` or a database client to inspect rows.
    - If tokens are not issued, confirm you are using the correct client and grant type for the request (password or personal).

- Common fixes:
    - After changing `.env`, key files, or config, run: `php artisan config:clear` and `php artisan cache:clear`.
    - Restart PHP-FPM or queue workers when keys or config change: `sudo systemctl restart php8.3-fpm` and `php artisan queue:restart`.
    - If you see network errors like `cURL error 7`, verify Nginx/Apache are running and the host/port are reachable.

- Logs and error details:
    - Tail the application log while reproducing the issue: `tail -f storage/logs/laravel.log`.
    - For HTTP-level troubleshooting, use `curl --verbose` or Postman to inspect request/response headers and bodies.

- Environment-stored keys (alternative):
    - If you prefer to store keys in environment variables instead of files, copy the private/public key contents into `.env` as `PASSPORT_PRIVATE_KEY` and `PASSPORT_PUBLIC_KEY` and update `config/passport.php` accordingly. After changing config, run `php artisan config:clear`.

- Recreating clients and tokens:
    - To recreate problematic clients, delete their rows from `oauth_clients` and run `php artisan passport:install` or create new clients with `php artisan passport:client`.

These commands and notes reflect how Passport is used in this repository and should help with setup and debugging.


### 10. Access the Application

Open your browser and go to:

```plaintext
http://revesta.local
```

---

## Testing

The application includes comprehensive automated tests to ensure code quality and reliability. Tests are completely isolated from your local development database.

### ⚡ Quick Test Commands

```bash
# Run all tests
php artisan test

# Run tests without coverage (faster)
php artisan test --no-coverage

# Run specific test file
php artisan test tests/Feature/Controllers/AddressControllerTest.php

# Run tests matching a pattern
php artisan test --filter="address"

# Show verbose output
php artisan test --verbose
```

### 🔒 Database Safety

- Tests use a **separate MySQL database** (`revesta_testing`)
- Your local development database (`revesta_db`) is **never modified**
- Each test gets a fresh, clean database state via the `RefreshDatabase` trait
- Database is automatically migrated and reset between tests

### 📋 Test Suite

**16+ test files** covering:
- ✅ Authentication (login, registration, password reset, email verification)
- ✅ Address management (CRUD, search, validation, label generation)
- ✅ Form validation
- ✅ JSON API endpoints
- ✅ Database connections and integrity

### 📖 Full Documentation

For comprehensive testing documentation including:
- Testing architecture and how it works
- Debugging failed tests with examples
- Writing new tests following best practices
- Troubleshooting common issues
- Performance optimization tips

**See: [TESTING.md](TESTING.md)** (1170+ lines of detailed guidance)

### 🛠️ Setup One-Time (First Time Only)

```bash
# Create the test database
mysql -u root -proot -e "CREATE DATABASE IF NOT EXISTS revesta_testing;"

# Verify it was created
mysql -u root -proot -e "SHOW DATABASES;" | grep revesta_testing
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
