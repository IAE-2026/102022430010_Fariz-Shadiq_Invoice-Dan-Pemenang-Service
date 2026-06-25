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

# Set permissions
RUN chmod -R 777 storage bootstrap/cache

# Setup SQLite database if it doesn't exist
RUN mkdir -p database && touch database/database.sqlite && chmod 777 database/database.sqlite

EXPOSE 8000

CMD php artisan serve --host=0.0.0.0 --port=8000
