FROM php:8.2-apache

# Laravel harus serve dari /public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# System deps + PHP extensions + Apache rewrite + set docroot + allow .htaccess
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libjpeg62-turbo-dev \
    libzip-dev zip unzip git curl \
    libonig-dev pkg-config \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql mbstring zip \
    && a2enmod rewrite \
    && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
        /etc/apache2/sites-available/*.conf \
        /etc/apache2/apache2.conf \
        /etc/apache2/conf-available/*.conf \
    && printf '%s\n' \
        '<Directory /var/www/html/public>' \
        '    AllowOverride All' \
        '    Require all granted' \
        '</Directory>' \
        > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel \
    && rm -rf /var/lib/apt/lists/*

# Node.js untuk build Vite
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get update && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
COPY . .

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHP deps (jangan pakai --no-scripts)
RUN composer install --no-dev --optimize-autoloader

# Build Vite assets -> menghasilkan public/build/manifest.json
# (kalau kamu pakai yarn/pnpm, ganti perintahnya)
RUN npm ci && npm run build

# Buat file SQLite + permission
RUN mkdir -p database \
    && touch database/database.sqlite \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache \
    && chmod 664 database/database.sqlite

# Entrypoint untuk migrate saat start
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80
CMD ["/entrypoint.sh"]
