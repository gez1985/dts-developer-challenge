FROM php:8.3-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libicu-dev \
    libpq-dev \
    zip \
    && docker-php-ext-install pdo pdo_pgsql intl zip

# Install Node.js (v18 LTS) and npm
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY . .

# Fix Git safe directory warning
RUN git config --global --add safe.directory /var/www/html

# Install Composer dependencies
RUN composer install --no-dev --optimize-autoloader

# Build frontend assets
RUN npm install && npm run build

# Set permissions for storage and cache
RUN chmod -R 775 storage bootstrap/cache

# Expose port
EXPOSE 8000

# Start the Laravel app
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
