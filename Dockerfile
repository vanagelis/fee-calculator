FROM php:8.3-fpm

RUN apt update \
    && apt install -y libzip-dev zip \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip

RUN docker-php-ext-install bcmath

WORKDIR /var/www/app

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
