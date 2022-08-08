FROM php:8.1.1-apache

ARG composer_dir=/usr/local/bin
ARG composer_filename=composer

RUN apt-get update \
  && apt-get install -y unzip

RUN docker-php-ext-install pdo_mysql

RUN echo '\
log_errors = On\n\
error_log = /dev/stderr\n\
error_reporting = E_ALL\n\
' >> /usr/local/etc/php/php.ini

ENV PORT 80
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
ENV BIGQUERY_DATASET ""
ENV BIGQUERY_CREDENTIAL ""

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN cd /etc/apache2/mods-enabled \
    && ln -s ../mods-available/rewrite.load

COPY ./docker/run.sh /tmp
ENTRYPOINT ["/tmp/run.sh"]
