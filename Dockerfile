# Use the official PHP image with FPM
FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libicu-dev libpq-dev zip curl gnupg \
    # Install dependencies for Node.js
    curl gnupg2 lsb-release ca-certificates \
    && curl -fsSL https://deb.nodesource.com/setup_16.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-install pdo pdo_pgsql intl zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www/html

# Copy app code
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install Node.js dependencies
RUN npm install

# Build the front-end assets
RUN npm run build

# Set correct permissions
RUN chmod -R 775 storage bootstrap/cache

# Expose port 9000 for PHP-FPM
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]
