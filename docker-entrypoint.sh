#!/bin/bash
# docker-entrypoint.sh

# Exit immediately if any command fails
set -e

# Check if APP_ENV is set to 'testing'
if [ "$APP_ENV" == "testing" ]; then
    echo "Testing mode: wiping vendor/ and composer.lock..."
    rm -rf vendor composer.lock || echo "Unable to remove vendor or composer.lock"
    echo "Running in testing environment. Installing development dependencies..."
    composer install --dev --optimize-autoloader
    composer dump-autoload --dev  # Ensure autoload for testing is included
else
    # Install Composer dependencies (without dev dependencies)
    composer install --no-dev --optimize-autoloader
fi

# Only generate APP_KEY if it's not already set
if [ -z "$APP_KEY" ]; then
  echo "Generating Laravel APP_KEY..."
  php artisan key:generate
else
  echo "APP_KEY already set. Skipping key generation."
fi

# Install Node.js dependencies and build assets (skip in testing)
if [ "$APP_ENV" != "testing" ]; then
    npm install
    npm run build
fi

# Run migrations
php artisan migrate --force

# Run tests if this container is just for testing
if [ "$APP_ENV" == "testing" ]; then
    echo "Running tests..."
    php artisan test
    exit $?  # Return exit code from tests
fi

# Start PHP-FPM
exec php-fpm
