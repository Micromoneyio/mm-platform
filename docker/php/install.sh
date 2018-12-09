#!/usr/bin/env bash

chmod -R 777 /var/www/app/storage
chmod -R 777 /var/www/app/bootstrap/cache

composer install

php artisan key:generate
php artisan migrate --seed
php artisan config:cache
php artisan config:sign
php artisan schedule:run