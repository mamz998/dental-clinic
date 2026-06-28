#!/usr/bin/env bash
set -e

echo "=== Installing PHP dependencies ==="
composer install --no-dev --optimize-autoloader

echo "=== Installing Node dependencies ==="
npm ci

echo "=== Building frontend assets ==="
npm run build

echo "=== Setting up SQLite database ==="
mkdir -p /var/data
touch /var/data/database.sqlite

export DB_DATABASE=/var/data/database.sqlite

echo "=== Running migrations ==="
php artisan migrate --force

echo "=== Seeding database ==="
php artisan db:seed --force

echo "=== Caching config/routes/views ==="
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== Build complete ==="
