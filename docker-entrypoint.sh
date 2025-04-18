#!/bin/bash

# Run Composer install to install PHP dependencies (only if not installed yet)
echo "Running Composer install..."
composer install --no-dev --optimize-autoloader

# Run Laravel migrations
echo "Running Laravel migrations..."
php artisan migrate --force

# Run the original command to start PHP-FPM
exec php-fpm
