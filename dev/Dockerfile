FROM dunglas/frankenphp:latest

RUN install-php-extensions pdo pdo_mysql

COPY php.ini /usr/local/etc/php/conf.d/99-errors.ini

WORKDIR /var/www/html

EXPOSE 80

CMD ["frankenphp", "php-server"]