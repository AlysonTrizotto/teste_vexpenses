FROM php:8.3-cli-alpine AS build

# Instala dependências de build
RUN apk add --no-cache --virtual .php_build_deps $PHPIZE_DEPS autoconf && \
    apk add --no-cache \
        libstdc++ \
        brotli-dev \
        bash \
        git \
        linux-headers \
        libzip-dev \
        libxml2-dev \
        supervisor \
        nodejs \
        npm \
        icu-dev \
        zlib-dev \
        mysql-client \
        mariadb-dev


# Configura e instala extensões PHP
RUN docker-php-ext-configure pcntl --enable-pcntl && \
    docker-php-ext-install \
        bcmath \
        ctype \
        pcntl \
        soap \
        intl \
        sockets \
        zip \
        pdo_mysql

# Copia o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define diretório de trabalho e copia o projeto
WORKDIR /app
COPY . .

# Instala dependências do Laravel
RUN composer install --no-dev --no-interaction --prefer-dist

# Instala o Octane com FrankenPHP
RUN php artisan octane:install --server=frankenphp

# Compila os assets do front
RUN npm install && npm run build


# ===================================================
# SEGUNDA FASE: imagem final com FrankenPHP
# ===================================================

FROM dunglas/frankenphp:latest-php8.3-alpine

# Instala dependências de runtime
RUN apk add --no-cache --virtual .php_runtime_deps $PHPIZE_DEPS autoconf && \
    apk add --no-cache \
        bash \
        mariadb-dev \
        linux-headers \
        libzip-dev \
        libxml2-dev \
        zlib-dev \
        pcre-dev \
        supervisor && \
    docker-php-ext-configure pcntl --enable-pcntl && \
    docker-php-ext-install pcntl pdo_mysql && \
    pecl update-channels && \
    pecl install redis && \
    docker-php-ext-enable redis && \
    apk del .php_runtime_deps


# Copia aplicação compilada do estágio anterior
COPY --from=build /app /app

# Copia arquivos de supervisão
COPY scripts/supervisord/supervisord.conf /etc/supervisord.conf
COPY scripts/supervisord/supervisord-laravel.conf /etc/supervisor/conf.d/supervisord-laravel.conf

# Define diretório de trabalho
WORKDIR /app


# Permite execução do entrypoint
RUN chmod +x scripts/entrypoint.sh

EXPOSE 80 443 8030
