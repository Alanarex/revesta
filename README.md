## **📜 Revesta Installation & Setup Guide**

# Revesta - Laravel ERP Installation & Server Setup

## 📌 Requirements
Before proceeding with the installation, ensure your system meets the following requirements:

- **OS:** Ubuntu (WSL or Native Linux)
- **Web Server:** Nginx
- **Database:** MySQL 8.0+
- **PHP Version:** PHP 8.4
- **PHP Extensions:**
  - `php8.4-mysql`
  - `php8.4-xml`
  - `php8.4-mbstring`
  - `php8.4-curl`
  - `php8.4-bcmath`
  - `php8.4-zip`
  - `php8.4-tokenizer`
- **Other Dependencies:**
  - Composer
  - Node.js & npm (for frontend assets, if applicable)

---

## 📌 Step 1: Update System
Ensure your system is up to date:

```sh
sudo apt update && sudo apt upgrade -y
```

---

## 📌 Step 2: Install Required Packages

### **🔹 Install PHP & Extensions**
```sh
sudo apt install -y php8.4 php8.4-cli php8.4-fpm php8.4-mysql php8.4-xml php8.4-mbstring php8.4-curl php8.4-bcmath php8.4-zip php8.4-tokenizer unzip
```

### **🔹 Install Nginx**
```sh
sudo apt install -y nginx
sudo systemctl enable nginx
sudo systemctl start nginx
```

### **🔹 Install MySQL**
```sh
sudo apt install -y mysql-server
sudo systemctl enable mysql
sudo systemctl start mysql
```

#### **Configure MySQL for Laravel**
```sh
sudo mysql -u root
```
Then, run:
```sql
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '';
FLUSH PRIVILEGES;
CREATE DATABASE revesta_db;
EXIT;
```

---

## 📌 Step 3: Install Composer & Laravel

### **🔹 Install Composer**
```sh
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### **🔹 Install Laravel (Inside `/var/www/revesta/`)**
```sh
cd /var/www
sudo mkdir revesta && cd revesta
sudo chown -R $USER:$USER /var/www/revesta
composer create-project --prefer-dist laravel/laravel .
```

---

## 📌 Step 4: Configure Laravel

### **🔹 Set Environment Variables**
```sh
cp .env.example .env
nano .env
```
Modify the `.env` file to match MySQL credentials:
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=revesta_db
DB_USERNAME=root
DB_PASSWORD=
```
Then, generate the application key:
```sh
php artisan key:generate
```

---

## 📌 Step 5: Set Permissions

```sh
sudo chown -R www-data:www-data /var/www/revesta/storage /var/www/revesta/bootstrap/cache
sudo chmod -R 775 /var/www/revesta/storage /var/www/revesta/bootstrap/cache
```

---

## 📌 Step 6: Configure Nginx

Create a new Nginx configuration file:
```sh
sudo nano /etc/nginx/sites-available/revesta
```

Add the following content:
```nginx
server {
    listen 80;
    server_name revesta.local;
    root /var/www/revesta/public;
    index index.php index.html index.htm;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```
Save and exit.

Enable the site and restart Nginx:
```sh
sudo ln -s /etc/nginx/sites-available/revesta /etc/nginx/sites-enabled/
sudo systemctl restart nginx
```

---

## 📌 Step 7: Configure `/etc/hosts` (Local Access)

Edit the hosts file:
```sh
sudo nano /etc/hosts
```
Add this line:
```
127.0.0.1 revesta.local
```
Save and exit.

---

## 📌 Step 8: Migrate Database
```sh
php artisan migrate
```

---

## 📌 Step 9: Restart Services

```sh
sudo systemctl restart mysql
sudo systemctl restart php8.4-fpm
sudo systemctl restart nginx
```

---

## 📌 Step 10: Test Laravel
Visit:

```
http://revesta.local
```

If it works, the setup is successful! 🎉

---

## **💡 Troubleshooting**
| Issue | Solution |
|--------|----------|
| **Site not loading?** | Check Nginx logs: `sudo tail -f /var/log/nginx/error.log` |
| **MySQL errors?** | Run `sudo mysql_secure_installation` and ensure credentials in `.env` are correct |
| **Permissions issues?** | Run: `sudo chown -R www-data:www-data /var/www/revesta/storage /var/www/revesta/bootstrap/cache` |
| **PHP-FPM errors?** | Restart PHP-FPM: `sudo systemctl restart php8.4-fpm` |
| **DNS error (revesta.local not found)?** | Ensure `127.0.0.1 revesta.local` is in `/etc/hosts`, and run `ipconfig /flushdns` (on Windows) |

---

## **🚀 Additional Setup**
### **🔹 Installing Node.js & npm (Optional, for Frontend)**
```sh
sudo apt install -y nodejs npm
```
Install dependencies:
```sh
npm install
```
Compile assets:
```sh
npm run dev
```

---

### **📝 Next Steps**
1. **Copy this file to your project**:
   ```sh
   nano /var/www/revesta/README.md
   ```
   Paste the content and save.

2. **Commit to Git (if using Git)**
   ```sh
   git add README.md
   git commit -m "Added installation and setup guide"
   git push origin main
   ```