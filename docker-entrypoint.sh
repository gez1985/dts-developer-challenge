#!/bin/bash
# docker-entrypoint.sh

# Exit immediately if any command fails
set -e

# Install Composer dependencies if not already installed
composer install --no-dev --optimize-autoloader

# Install Node.js dependencies and build assets
npm install
npm run build

# Start PHP-FPM
exec php-fpm