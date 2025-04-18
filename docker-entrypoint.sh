#!/bin/bash
set -e

echo "Running Composer install..."
composer install --no-dev --optimize-autoloader

echo "Running Laravel migrations..."
php artisan migrate --force

echo "Starting PHP-FPM..."
exec php-fpm
