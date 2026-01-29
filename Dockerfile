FROM php:5.6-apache

RUN docker-php-ext-install pdo pdo_mysql

COPY php.ini /usr/local/etc/php/conf.d/99-errors.ini
COPY apache.conf /etc/apache2/sites-enabled/000-default.conf

WORKDIR /var/www/html