# ==============================================================================
# STAGE 1: FRONTEND ASSET BUILDER (NODE.JS + TAILWIND)
# ==============================================================================
FROM node:20-alpine AS assets-builder

WORKDIR /app
COPY package*.json ./
RUN npm ci --prefer-offline --no-audit

COPY . .
RUN npm run build || true

# ==============================================================================
# STAGE 2: PRODUCTION PHP 8.3-FPM + NGINX CONTAINER
# ==============================================================================
FROM php:8.3-fpm-alpine

LABEL maintainer="PO-in Aja Architect"

# Install System Packages & Libraries
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    git \
    zip \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    postgresql-dev \
    icu-dev \
    oniguruma-dev

# Configure & Install PHP Extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        pgsql \
        gd \
        zip \
        intl \
        bcmath \
        opcache

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy Project Files
COPY . .
COPY --from=assets-builder /app/public/build ./public/build

# Copy Docker Configuration Files
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/custom-production.ini
COPY entrypoint.sh /usr/local/bin/entrypoint.sh

# Install Composer Dependencies for Production
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Set permissions
RUN chmod +x /usr/local/bin/entrypoint.sh \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
