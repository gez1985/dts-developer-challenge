#!/bin/bash
# docker-entrypoint.sh

# Exit immediately if any command fails
set -e

# Install Composer dependencies if not already installed
composer install --no-dev --optimize-autoloader

# Only generate APP_KEY if it's not already set
if [ -z "$APP_KEY" ]; then
  echo "Generating Laravel APP_KEY..."
  php artisan key:generate
else
  echo "APP_KEY already set. Skipping key generation."
fi

# Install Node.js dependencies and build assets
npm install
npm run build

# Run migrations
php artisan migrate

# Start PHP-FPM
exec php-fpm