#!/bin/sh

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link --force
php artisan migrate --force

nginx
php-fpm
