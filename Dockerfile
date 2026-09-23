FROM richarvey/nginx-php-fpm:latest

# Install PostgreSQL dev tools and pdo_pgsql extension
RUN apk --no-cache add postgresql-dev \
    && docker-php-ext-install pdo_pgsql

# Copy project files
COPY . .

# Set writable permissions for storage and bootstrap cache
RUN chmod -R 777 storage bootstrap/cache

# Set environment
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr
ENV COMPOSER_ALLOW_SUPERUSER 1

CMD ["/start.sh"]
