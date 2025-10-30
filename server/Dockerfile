# stage 1: Composer (build vendor)
FROM composer:2 AS vendor
WORKDIR /app
COPY . /app
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress

#stage 2: Runtime (PHP-FPM + Node + SQLite)
FROM php:8.3-fpm-alpine

# System deps + PHP extensions (tambahkan $PHPIZE_DEPS agar pcntl bisa build)
RUN apk add --no-cache curl zip unzip bash icu-dev oniguruma-dev libzip-dev sqlite sqlite-dev nodejs npm $PHPIZE_DEPS \
 && docker-php-ext-configure zip \
 && docker-php-ext-install intl mbstring opcache bcmath pdo_sqlite zip pcntl
 
# Composer binary in runtime image
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# All work happens here
WORKDIR /var/www/html

# Frontend build with cache friendliness
# Copy only necessary file first for npm to install
COPY package.json package-lock.json* vite.config.js postcss.config.js tailwind.config.js ./
COPY resources/ resources/
RUN npm install && npm run build

# Copy the rest of the app + vendor from composer stage
COPY . .
COPY --from=vendor /app/vendor ./vendor

ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS=1

# Writable dirs
RUN mkdir -p storage bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
