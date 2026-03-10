FROM php:8.4-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    git curl zip unzip \
    libpng-dev libjpeg-turbo-dev freetype-dev oniguruma-dev libzip-dev \
    nodejs npm

# Configure & Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_mysql bcmath gd exif pcntl zip

# Copy Composer terbaru
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Salin source code untuk instalasi dependensi saat build
COPY . /var/www

# Buat direktori cache Laravel dan .env sementara untuk build
RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache bootstrap/cache \
    && cp .env.example .env

# Instalasi dependensi PHP & Node saat build
RUN composer install --no-interaction --prefer-dist --optimize-autoloader \
    && npm install

# MacOS menghandle permission dengan baik, cukup gunakan default www-data jika perlu
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Langsung jalankan php-fpm
CMD ["php-fpm"]