FROM php:8.2-fpm

# Install dependencies and extensions
RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev zip unzip libzip-dev libpq-dev procps && \
    apt-get clean && rm -rf /var/lib/apt/lists/* && \
    docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configure PHP-FPM
RUN echo "[global]" > /usr/local/etc/php-fpm.d/zz-docker.conf && \
    echo "daemonize = no" >> /usr/local/etc/php-fpm.d/zz-docker.conf && \
    echo "[www]" >> /usr/local/etc/php-fpm.d/zz-docker.conf && \
    echo "user = www-data" >> /usr/local/etc/php-fpm.d/zz-docker.conf && \
    echo "group = www-data" >> /usr/local/etc/php-fpm.d/zz-docker.conf && \
    echo "listen = 9000" >> /usr/local/etc/php-fpm.d/zz-docker.conf && \
    echo "listen.owner = www-data" >> /usr/local/etc/php-fpm.d/zz-docker.conf && \
    echo "listen.group = www-data" >> /usr/local/etc/php-fpm.d/zz-docker.conf && \
    echo "pm = dynamic" >> /usr/local/etc/php-fpm.d/zz-docker.conf && \
    echo "pm.max_children = 5" >> /usr/local/etc/php-fpm.d/zz-docker.conf && \
    echo "pm.start_servers = 2" >> /usr/local/etc/php-fpm.d/zz-docker.conf && \
    echo "pm.min_spare_servers = 1" >> /usr/local/etc/php-fpm.d/zz-docker.conf && \
    echo "pm.max_spare_servers = 3" >> /usr/local/etc/php-fpm.d/zz-docker.conf && \
    echo "catch_workers_output = yes" >> /usr/local/etc/php-fpm.d/zz-docker.conf && \
    echo "decorate_workers_output = no" >> /usr/local/etc/php-fpm.d/zz-docker.conf && \
    echo "access.log = /proc/self/fd/2" >> /usr/local/etc/php-fpm.d/zz-docker.conf && \
    echo "clear_env = no" >> /usr/local/etc/php-fpm.d/zz-docker.conf

# Remove default config
RUN rm /usr/local/etc/php-fpm.d/www.conf

# Set working directory and permissions
WORKDIR /var/www/backend
COPY backend/ ./
RUN chown -R www-data:www-data /var/www/backend && \
    chmod -R 775 /var/www/backend/storage

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Health check
HEALTHCHECK --interval=5s --timeout=3s --start-period=1s \
  CMD php -r "echo file_get_contents('http://localhost:9000/status');"

CMD ["php-fpm", "-F", "-R"]
