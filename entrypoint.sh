#!/bin/sh
set -e

# Pastikan sqlite ada (kalau container baru)
if [ ! -f /var/www/html/database/database.sqlite ]; then
  mkdir -p /var/www/html/database
  touch /var/www/html/database/database.sqlite
fi

# Permission minimal
chown -R www-data:www-data /var/www/html || true

# Clear cache biar env baru kebaca
php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true
php artisan route:clear || true

# Migrate (pakai --force di production)
php artisan migrate --force || true

exec apache2-foreground
