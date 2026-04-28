FROM php:8.5-cli-bookworm

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        ca-certificates \
        curl \
        git \
        gnupg \
        libzip-dev \
        unzip \
        zip \
    && docker-php-ext-install pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get update \
    && apt-get install -y --no-install-recommends nodejs \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

COPY package.json package-lock.json ./
RUN npm ci

COPY . .

RUN php artisan package:discover --ansi \
    && npm run build \
    && php artisan config:clear \
    && php artisan route:clear \
    && php artisan view:clear

EXPOSE 10000

CMD ["sh", "-lc", "php artisan serve --host 0.0.0.0 --port ${PORT:-10000}"]
