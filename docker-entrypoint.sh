#!/bin/bash
# docker-entrypoint.sh

# Exit immediately if any command fails
set -e

# Ensure permissions are correct
chown -R www-data:www-data /var/www/html

# Install Composer dependencies (if not already installed)
composer install --no-dev --optimize-autoloader

# Install Node.js dependencies and build assets
npm install
npm run build

# Any other logic you want to run before starting the application
exec "$@"
