FROM php:8.2-fpm

WORKDIR /app


RUN apt-get update
RUN apt-get install -y zlib1g-dev
RUN apt-get install -y libmemcached-dev
RUN apt-get install -y libssl-dev

RUN pecl install memcached-3.2.0

RUN docker-php-ext-enable memcached

RUN docker-php-ext-install mysqli
RUN docker-php-ext-install pdo_mysql


CMD ["php-fpm"]