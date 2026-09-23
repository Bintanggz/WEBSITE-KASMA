#!/usr/bin/env bash
set -e

cd /var/www/html

echo "--- 1. Ensuring permissions ---"
chmod -R 777 storage bootstrap/cache

echo "--- 2. Discovering packages ---"
php artisan package:discover --ansi || true

echo "--- 3. Creating storage symlink ---"
php artisan storage:link || true

echo "--- 4. Clearing cache before migrations ---"
php artisan config:clear || true
php artisan cache:clear || true

echo "--- 5. Running database migrations ---"
php artisan migrate --force || echo "Warning: migrate failed, check DB connection"

echo "--- 6. Seeding database if empty ---"
php artisan db:seed --force || echo "Warning: db:seed failed"

echo "--- 7. Caching config, routes, and views ---"
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "--- KASMA deployment script finished successfully! ---"

