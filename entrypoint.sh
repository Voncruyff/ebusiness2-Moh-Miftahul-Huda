#!/bin/sh
set -e

# Pastikan sqlite ada (kalau container baru)
if [ ! -f /var/www/html/database/database.sqlite ]; then
  mkdir -p /var/www/html/database
  touch /var/www/html/database/database.sqlite
fi

# Pastikan folder penting ada
mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache

# Permission (storage & cache harus writable)
chown -R www-data:www-data /var/www/html || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true

# Clear cache biar env baru kebaca
php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true
php artisan route:clear || true

# Migrate & seed
php artisan migrate --force
php artisan db:seed --force

# INI YANG BIKIN GAMBAR BISA DIACCESS: /public/storage -> storage/app/public
php artisan storage:link || true

exec apache2-foreground
