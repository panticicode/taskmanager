#!/bin/bash
set -e

echo "Starting Deployment..."

# Set proper permissions
sudo chmod -R 755 /var/www/html/tasks/

# Navigate to the project directory
cd /var/www/html/tasks

# Install composer dependencies
composer install --no-interaction --prefer-dist --optimize-autoloader

# Move .env.local to .env if needed
if [ -f ".env.local" ]; then
  sudo mv .env.local .env
fi

# Set permissions again (optional)
sudo chmod -R 755 /var/www/html/tasks/

# Run database migrations and seed the database
php artisan migrate --seed --force

# Install npm dependencies and build assets
npm install
npm run build

echo "Deployment Finished!"
