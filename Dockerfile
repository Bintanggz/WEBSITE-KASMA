FROM richarvey/nginx-php-fpm:latest

# Install PostgreSQL dev tools and pdo_pgsql extension
RUN apk --no-cache add postgresql-dev \
    && docker-php-ext-install pdo_pgsql

# Configure Nginx FastCGI buffers to prevent 502 Bad Gateway
RUN mkdir -p /etc/nginx/conf.d \
    && echo "fastcgi_buffer_size 128k; fastcgi_buffers 4 256k; fastcgi_busy_buffers_size 256k;" > /etc/nginx/conf.d/fastcgi_buffer.conf

# Copy project files
COPY . .

# Install composer dependencies during build
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Ensure scripts and storage permissions
RUN chmod +x scripts/*.sh \
    && chmod -R 777 storage bootstrap/cache

# Set environment
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_CATCHALL 1
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr
ENV COMPOSER_ALLOW_SUPERUSER 1

CMD ["/start.sh"]
