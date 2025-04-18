# Use official PHP image with FPM
FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libicu-dev libpq-dev zip curl gnupg \
    && docker-php-ext-install pdo pdo_pgsql intl zip

# Install Node.js (version 22.14) and npm
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY . .

# Set correct permissions for storage and cache directories
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Install Node.js dependencies
RUN npm install

# Build frontend assets
RUN npm run build

# Set correct permissions
RUN chmod -R 775 storage bootstrap/cache

# Copy the entrypoint script into the container
COPY docker-entrypoint.sh /usr/local/bin/

# Make the entrypoint script executable
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Set the entrypoint to the script
ENTRYPOINT ["docker-entrypoint.sh"]

# Expose port 9000 for PHP-FPM
EXPOSE 9000

# CMD to start PHP-FPM (this will be the last command executed by the entrypoint)
CMD ["php-fpm"]

