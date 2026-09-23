#!/usr/bin/env bash
echo "Running Laravel deploy script..."

echo "Ensuring storage permissions..."
chmod -R 777 storage bootstrap/cache

echo "Installing composer dependencies..."
composer install --no-dev --optimize-autoloader

echo "Creating storage symlink..."
php artisan storage:link || true

echo "Caching config, routes, and views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Running migrations..."
php artisan migrate --force

echo "Seeding initial accounts if not seeded..."
php artisan db:seed --force
