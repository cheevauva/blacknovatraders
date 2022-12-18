FROM php:8.2-cli

WORKDIR /app


RUN apt-get update
RUN apt-get install -y libxml2-dev
RUN apt-get install -y libzip-dev
RUN apt-get install -y zlib1g-dev
RUN apt-get install -y nano

RUN docker-php-ext-install zip

RUN php -r "copy('https://getcomposer.org/installer', '/composer-setup.php');" && \
    php /composer-setup.php  --version=2.2.6 --install-dir=/usr/local/bin && \
    php -r "unlink('/composer-setup.php');" 