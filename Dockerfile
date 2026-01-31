FROM php:8.2-fpm

# system deps + nodejs
RUN apt-get update && apt-get install -y \
    nginx git unzip curl ca-certificates \
    libzip-dev libpng-dev libonig-dev libxml2-dev \
 && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
 && apt-get install -y nodejs \
 && docker-php-ext-install pdo_mysql mbstring zip gd \
 && rm -rf /var/lib/apt/lists/*

# composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . /app

# php deps
RUN composer install --no-dev --optimize-autoloader

# frontend build (kalau pakai Vite)
RUN npm install && npm run build

# laravel folders + permission (ini wajib biar ga "valid cache path" lagi)
RUN mkdir -p storage/framework/{views,cache,sessions} bootstrap/cache storage/app/public/products \
 && chmod -R 775 storage bootstrap/cache

# storage symlink
RUN php artisan storage:link || true

# nginx config
COPY ./deploy/nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 8080

CMD php-fpm -D && nginx -g 'daemon off;'
