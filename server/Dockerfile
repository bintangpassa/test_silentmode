FROM composer:2 AS vendor
WORKDIR /app
COPY . /app
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress

FROM php:8.3-fpm-alpine

RUN apk add --no-cache nginx curl zip unzip bash icu-dev oniguruma-dev libzip-dev sqlite sqlite-dev \
 && docker-php-ext-install intl mbstring opcache bcmath \
 && docker-php-ext-install pdo pdo_sqlite \
 && apk add --no-cache libzip-dev \
 && docker-php-ext-configure zip \
 && docker-php-ext-install zip 

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html

COPY . /var/www/html
COPY --from=vendor /app/vendor /var/www/html/vendor

ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS=1

RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache \
 && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
