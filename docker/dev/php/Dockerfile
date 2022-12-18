FROM php:5.4-fpm

WORKDIR /app


RUN apt-get update

RUN docker-php-ext-install mysqli
RUN docker-php-ext-install pdo_mysql


CMD ["php-fpm"]