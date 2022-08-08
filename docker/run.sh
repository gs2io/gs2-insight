#!/bin/sh

cd /var/www/html
php ./composer.phar install
php artisan migrate --force

if test "$BIGQUERY_DATASET" != "" -a "$BIGQUERY_CREDENTIAL" != ""; then
    php artisan command:setup "$BIGQUERY_DATASET" "$BIGQUERY_CREDENTIAL" "`date -d '1weeks ago' +%Y-%m-%dT%H:%M:%S%z`" "`date +%Y-%m-%dT%H:%M:%S%z`"
    php artisan command:populate
fi

php artisan serve --host=0.0.0.0 --port=$PORT
