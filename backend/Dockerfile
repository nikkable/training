FROM php:8.3-fpm

RUN apt-get update && \
    apt-get install -y \
        nginx \
        mariadb-client \
        curl \
        git \
        supervisor \
        openssl \
        libzip-dev \
        libonig-dev \
        libicu-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libgmp-dev \
        libwebp-dev \
        libxml2-dev \
        && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql opcache bcmath gd exif pcntl zip gmp intl mbstring soap

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
COPY . .
RUN composer install --no-dev --optimize-autoloader
RUN php artisan key:generate || true
RUN php artisan config:clear && php artisan cache:clear
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type f -exec chmod 664 {} \; \
    && find /var/www/html -type d -exec chmod 775 {} \; \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
EXPOSE 80
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
