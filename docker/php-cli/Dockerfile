FROM composer:2.8

RUN apk add --no-cache $PHPIZE_DEPS postgresql-dev git unzip \
    && docker-php-ext-install pdo_pgsql

WORKDIR /var/www/html
