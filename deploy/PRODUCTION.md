# Revesta Admin Production

## Domaine

DNS OVH attendu :

```dns
admin.revesta.fr.  A  31.207.38.67
```

Verification :

```bash
dig +short admin.revesta.fr
curl -I https://admin.revesta.fr/up
```

## Nginx

Pour Laravel, preferer PHP-FPM plutot qu'un reverse proxy vers `php artisan serve`.

```bash
sudo cp deploy/nginx-admin-revesta.conf /etc/nginx/sites-available/admin.revesta.fr
sudo ln -sf /etc/nginx/sites-available/admin.revesta.fr /etc/nginx/sites-enabled/admin.revesta.fr
sudo nginx -t
sudo systemctl reload nginx
```

Si le projet est installe dans `/var/www/revesta` et non `/var/www/revesta-admin`, adapter `root` :

```nginx
root /var/www/revesta/public;
```

Si PHP-FPM utilise une autre version, verifier le socket :

```bash
ls /run/php
```

Puis adapter :

```nginx
fastcgi_pass unix:/run/php/php8.2-fpm.sock;
```

## Laravel

```bash
cd /var/www/revesta-admin
cp deploy/production.env.example .env
php artisan key:generate
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan passport:keys --force
php artisan passport:client --password --name="Revesta Password Client"
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Reporter l'id et le secret du client Passport dans `.env` :

```env
PASSPORT_PASSWORD_CLIENT_ID=
PASSPORT_PASSWORD_CLIENT_SECRET=
```

Permissions :

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo find storage bootstrap/cache -type d -exec chmod 775 {} \;
sudo find storage bootstrap/cache -type f -exec chmod 664 {} \;
```

## Queue

```bash
sudo mkdir -p /var/log/revesta
sudo chown www-data:www-data /var/log/revesta
sudo cp deploy/supervisor-revesta-admin.conf /etc/supervisor/conf.d/revesta-admin-worker.conf
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl restart revesta-admin-worker:*
```

## Mail

Recommandation : utiliser un SMTP transactionnel ou la boite OVH, pas un serveur mail auto-heberge sur le VPS.

Variables Laravel :

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=no-reply@revesta.fr
MAIL_PASSWORD=secret
MAIL_SCHEME=tls
MAIL_FROM_ADDRESS=no-reply@revesta.fr
MAIL_FROM_NAME="Revesta"
CONTACT_EMAIL=contact@revesta.fr
```

Pour une boite OVH/MX Plan, les valeurs courantes sont souvent :

```env
MAIL_HOST=ssl0.ovh.net
MAIL_PORT=587
MAIL_SCHEME=tls
MAIL_USERNAME=no-reply@revesta.fr
MAIL_PASSWORD=mot-de-passe-boite
```

DNS mail minimum pour envoyer proprement depuis `revesta.fr` :

```dns
revesta.fr.       TXT  "v=spf1 include:mx.ovh.com ~all"
_dmarc.revesta.fr TXT  "v=DMARC1; p=none; rua=mailto:postmaster@revesta.fr"
```

Ajouter aussi le DKIM fourni par le prestataire email choisi. Sans DKIM, les emails peuvent arriver en spam.

Test rapide :

```bash
php artisan tinker
Mail::raw('Test Revesta production', fn ($m) => $m->to('ton-email@example.com')->subject('Test mail Revesta'));
```

Apres modification mail :

```bash
php artisan config:clear
php artisan config:cache
sudo supervisorctl restart revesta-worker:*
```

## Checks

```bash
php artisan about --only=environment
php artisan migrate:status
php artisan queue:failed
curl -I https://admin.revesta.fr/up
tail -f storage/logs/laravel-$(date +%F).log
```
