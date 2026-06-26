FROM php:8.2-alpine

# Install system dependencies & PHP extensions
RUN apk add --no-cache \
    sqlite-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo pdo_sqlite zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . /app

# Install dependencies using Composer
RUN composer install --no-interaction --optimize-autoloader

# Setup .env file
RUN cp -n .env.example .env || true
RUN php artisan key:generate

# Setup SQLite database if it doesn't exist
RUN mkdir -p database && touch database/database.sqlite

# Set permissions
RUN chmod -R 777 storage bootstrap/cache database

EXPOSE 8000

CMD php artisan migrate --force && php artisan db:seed --force && php artisan serve --host=0.0.0.0 --port=8000
