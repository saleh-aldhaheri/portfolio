# syntax=docker/dockerfile:1

FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader \
    --ignore-platform-req=ext-intl


FROM node:22-slim AS assets

WORKDIR /app

ARG VITE_APP_NAME
ARG VITE_REVERB_APP_KEY
ARG VITE_REVERB_HOST
ARG VITE_REVERB_PORT
ARG VITE_REVERB_SCHEME

ENV VITE_APP_NAME=$VITE_APP_NAME \
    VITE_REVERB_APP_KEY=$VITE_REVERB_APP_KEY \
    VITE_REVERB_HOST=$VITE_REVERB_HOST \
    VITE_REVERB_PORT=$VITE_REVERB_PORT \
    VITE_REVERB_SCHEME=$VITE_REVERB_SCHEME

COPY package.json package-lock.json ./
RUN npm ci

COPY --from=vendor /app/vendor ./vendor

COPY . .
RUN npm run build


FROM php:8.5-fpm AS app

WORKDIR /var/www/portfolio

COPY . /var/www/portfolio

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    nginx \
    libzip-dev \
    libicu-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype-dev \
    libonig-dev \
    libxml2-dev \
    default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg

RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mbstring \
    bcmath \
    intl \
    pcntl \
    zip \
    exif \
    gd

RUN pecl install redis && docker-php-ext-enable redis

#limit php process memory usage to 512M
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/zz-memory-limit.ini

RUN printf '[client]\nssl-verify-server-cert=0\n' > /etc/my.cnf

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY --from=assets /app/public/build /var/www/portfolio/public/build

COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
RUN rm -f /etc/nginx/sites-enabled/default

RUN cp .env.example .env \
    && composer install \
        --no-dev \
        --no-interaction \
        --optimize-autoloader \
    && php artisan package:discover --ansi \
    && php artisan filament:assets \
    && composer clear-cache \
    && rm -f .env

RUN chmod +x ./scripts/entrypoint.sh

ENTRYPOINT ["./scripts/entrypoint.sh"]
CMD ["serve"]
