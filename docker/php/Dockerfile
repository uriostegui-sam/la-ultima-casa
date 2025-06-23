FROM php:8.2-fpm

# Dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libpq-dev

# Cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# PHP's extensions
RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Work directory
WORKDIR /var/www/backend

COPY backend/composer.* ./
RUN composer install --no-dev --optimize-autoloader

# Copy the rest of the backend
COPY backend/. .

# Prepare Laravel for production
RUN php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache \
 && php artisan storage:link