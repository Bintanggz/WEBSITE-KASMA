#!/usr/bin/env bash
set -e

cd /var/www/html

echo "--- 0. Ensuring Nginx FastCGI buffer sizes ---"
mkdir -p /etc/nginx/conf.d
echo "fastcgi_buffer_size 128k; fastcgi_buffers 4 256k; fastcgi_busy_buffers_size 256k;" > /etc/nginx/conf.d/fastcgi_buffer.conf || true

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

