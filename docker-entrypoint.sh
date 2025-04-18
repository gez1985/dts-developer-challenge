#!/bin/bash

set -e

# Set correct permissions at container start-up
echo "Setting permissions for storage and cache directories..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Run Composer install to install PHP dependencies (only if not installed yet)
echo "Running Composer install..."
composer install --no-dev --optimize-autoloader

# Run Laravel migrations
echo "Running Laravel migrations..."
php artisan migrate --force

# Start PHP-FPM
echo "Starting PHP-FPM..."
exec php-fpm
