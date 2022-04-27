#!/bin/sh

cd /var/www/html
php ./composer.phar install
php artisan migrate --force
php artisan serve --host=0.0.0.0 --port=$PORT
