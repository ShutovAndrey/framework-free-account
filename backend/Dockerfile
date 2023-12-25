FROM php:8.1-fpm

RUN apt-get update \
    && apt-get install -y \
    git libonig-dev libzip-dev libpq-dev build-essential \
    && docker-php-ext-install sockets pdo pdo_pgsql zip pdo_mysql mbstring zip exif pcntl

COPY . /var/www/html

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ENV COMPOSER_ALLOW_SUPERUSER 1

WORKDIR /var/www/html
RUN composer install 
RUN composer dump-autoload

EXPOSE 9000
CMD ["php-fpm"]

