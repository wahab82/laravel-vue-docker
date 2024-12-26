FROM php:8.2-fpm
WORKDIR /var/www
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    netcat-openbsd \
    && docker-php-ext-install zip pdo_mysql

RUN pecl install redis && docker-php-ext-enable redis
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
COPY . /var/www

RUN composer install --no-scripts --no-autoloader && \
    composer dump-autoload --optimize && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage

# Entrypoint script
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
# Expose port 9000 for PHP-FPM
EXPOSE 9000
ENTRYPOINT ["/entrypoint.sh"]
