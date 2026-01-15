FROM php:8.4-fpm

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y git unzip libzip-dev libonig-dev curl nodejs npm libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

RUN composer install

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
