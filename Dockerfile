FROM php:8.2.0-apache

WORKDIR /var/www/html

RUN a2enmod rewrite

RUN apt-get update -y && apt-get install -y libicu-dev unzip zip libpq-dev
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY . .

RUN docker-php-ext-install gettext intl pdo pdo_pgsql
