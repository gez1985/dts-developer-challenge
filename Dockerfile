# Use official PHP image with FPM
FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libicu-dev libpq-dev zip curl gnupg \
    && docker-php-ext-install pdo pdo_pgsql intl zip

# Install Node.js (version 22.x) and npm
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY . .

# Fix Git safe directory warning
RUN git config --global --add safe.directory /var/www/html

# Set correct permissions before switching to non-root user
RUN mkdir -p /var/log/php-fpm && \
    chown -R www-data:www-data /var/www/html /var/log/php-fpm && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/log/php-fpm

# Install Node.js dependencies and build assets
RUN npm install && npm run build

# Clean up Node.js dev dependencies and npm cache
RUN npm prune --production && npm cache clean --force

# Install Composer dependencies with --no-dev
RUN composer install --no-dev --optimize-autoloader && \
    composer clear-cache

# Fix permissions again in case pruning/installs modified them
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy the entrypoint script into the container
COPY docker-entrypoint.sh /usr/local/bin/

# Make the entrypoint script executable
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Switch to www-data user
USER www-data

# Set the entrypoint
ENTRYPOINT ["docker-entrypoint.sh"]

# Expose port 9000 for PHP-FPM
EXPOSE 9000

# Default command
CMD ["php-fpm"]
