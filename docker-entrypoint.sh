#!/bin/sh

# Wait for PostgreSQL to be ready
echo "Waiting for PostgreSQL..."
until nc -z postgres 5432; do
  sleep 2
done
echo "PostgreSQL is up - running migrations"

# Run migrations
composer install
php artisan key:generate
chown -R www-data:www-data .
chmod -R 777 .
php artisan migrate --force

# Check if public/storage exists, if not, create the symlink
if [ ! -d "/var/www/html/public/storage" ]; then
    echo "Creating storage symlink... ./public/storage"
    php artisan storage:link
else
    echo "Storage symlink already exists. Skipping."
fi

# Start PHP-FPM
php-fpm
