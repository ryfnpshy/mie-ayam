#!/bin/bash
set -e

# 1. Salin .env.example ke .env (untuk proses build)
cp .env.example .env

# 2. Instal dependensi Composer
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# 3. Generate APP_KEY sementara
php artisan key:generate --force --no-interaction

# 4. Optimize Laravel untuk production
php artisan config:cache --no-interaction
php artisan route:cache --no-interaction
php artisan view:cache --no-interaction

# 5. Build aset Vite
npm run build
