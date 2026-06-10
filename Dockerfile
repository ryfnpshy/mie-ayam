# Base image
FROM serversideup/php:8.4-fpm-nginx

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
USER root
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo_pgsql

# Copy project files
COPY --chown=www-data:www-data . .

# Install PHP dependencies
USER www-data
RUN composer install --no-dev --optimize-autoloader --prefer-dist --no-interaction

# Build assets (Vite)
USER root
RUN apt-get install -y nodejs npm
USER www-data
RUN npm install && npm run build

# Final touches
USER root
RUN php artisan view:cache

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port
EXPOSE 8080
