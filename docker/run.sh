#!/bin/sh

cd /var/www/html
php ./composer.phar install

mv .env.ecs .env

if test "$BASIC_AUTH_USER" != ""; then
    sed -e s/APP_BASIC_USER=/APP_BASIC_USER=$BASIC_AUTH_USER/g .env
fi

if test "$BASIC_AUTH_PASSWORD" != ""; then
    sed -e s/APP_BASIC_PASSWORD=/APP_BASIC_USER=$BASIC_AUTH_PASSWORD/g .env
fi

php artisan migrate --force

if test "$BIGQUERY_DATASET" != "" -a "$BIGQUERY_CREDENTIAL" != ""; then
    php artisan command:setup "$BIGQUERY_DATASET" "$BIGQUERY_CREDENTIAL" "`date -d '1weeks ago' +%Y-%m-%dT%H:%M:%S%z`" "`date +%Y-%m-%dT%H:%M:%S%z`"
    php artisan command:populate
fi

php artisan serve --host=0.0.0.0 --port=$PORT
