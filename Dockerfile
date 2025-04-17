FROM php:8.3-cli-alpine AS build

RUN apk add --no-cache --virtual php_dependencies $PHPIZE_DEPS && \
    apk add --no-cache  \
    libstdc++  \
    brotli-dev \
    libpq \
    bash \
    git \
    linux-headers \
    libzip-dev \
    libxml2-dev \
    supervisor \
    nodejs \
    npm \
    icu-dev \
    zlib-dev

RUN docker-php-ext-configure pcntl --enable-pcntl && \
    docker-php-ext-install \
        bcmath \
        ctype \
        pcntl \
        soap \
        intl \
        sockets \
        zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-dev --no-interaction --prefer-dist
RUN php artisan octane:install --server=frankenphp

RUN npm install && npm run build

FROM dunglas/frankenphp:latest-php8.3-alpine

RUN apk add --no-cache --virtual php_dependencies $PHPIZE_DEPS && \
    apk add --no-cache \
    bash  \
    zlib-dev  \
    postgresql-dev  \
    linux-headers \
    libzip-dev \
    libxml2-dev  \
    supervisor && \
    docker-php-ext-configure pcntl --enable-pcntl && \
    docker-php-ext-install pcntl pdo_pgsql && \
    apk del php_dependencies

COPY --from=build /app /app


COPY scripts/supervisord/supervisord.conf /etc/supervisord.conf
COPY scripts/supervisord/supervisord-laravel.conf /etc/supervisor/conf.d/supervisord-laravel.conf

WORKDIR /app

RUN chmod +x scripts/entrypoint.sh

EXPOSE 80 443 8030
