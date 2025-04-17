FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libicu-dev \
    libpq-dev \
    zip \
    && docker-php-ext-install pdo pdo_pgsql intl zip

RUN git config --global --add safe.directory /var/www/html

WORKDIR /var/www/html

# Copy only the composer files first, so we can install dependencies
COPY composer.json composer.lock ./

RUN composer install --no-dev --optimize-autoloader

# Now copy the rest of the application code
COPY . .

RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
