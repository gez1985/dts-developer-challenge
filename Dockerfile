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

# Copy the entrypoint script into the container
COPY docker-entrypoint.sh /usr/local/bin/

# Make the entrypoint script executable
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Set the entrypoint
ENTRYPOINT ["docker-entrypoint.sh"]

# Expose port 9000 for PHP-FPM
EXPOSE 9000

# Default command to run PHP-FPM
CMD ["php-fpm"]
