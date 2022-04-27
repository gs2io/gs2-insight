#!/bin/sh

cd /var/www/html
sleep 5
php artisan migrate
php artisan serve --host=0.0.0.0 --port=$PORT
