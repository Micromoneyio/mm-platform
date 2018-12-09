FROM php:7.2-fpm

ARG UID

RUN addgroup --gid $UID --system app \
  && adduser --uid $UID --system --disabled-login --disabled-password --gid $UID app

RUN apt-get update -yqq \
    && apt-get upgrade -y \
    && apt-get install -y --no-install-recommends apt-utils \
    && apt-get install -y --no-install-recommends cron dialog zip unzip git libgmp-dev re2c libmhash-dev libmcrypt-dev file curl libmemcached-dev libz-dev libpq-dev libjpeg-dev libfreetype6-dev libssl-dev sqlite3\
    && pecl channel-update pecl.php.net \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-install pdo pdo_mysql bcmath

# php ext-intl
RUN apt-get install -y zlib1g-dev libicu-dev g++ \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl

# php ext-gd
RUN docker-php-ext-configure gd \
        --enable-gd-native-ttf \
        --with-jpeg-dir=/usr/lib \
        --with-freetype-dir=/usr/include/freetype2 && \
    docker-php-ext-install gd

# Composer
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

# NodeJs
RUN apt-get install -y gnupg
RUN curl -sL https://deb.nodesource.com/setup_6.x -o nodesource_setup.sh
RUN bash nodesource_setup.sh
RUN apt-get install -y nodejs
RUN rm -f nodesource_setup.sh

# Copy xdebug configuration for remote debugging
COPY ./php.ini /usr/local/etc/php
COPY ./xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

COPY ./laravel.ini /usr/local/etc/php/conf.d
COPY ./xlaravel.pool.conf /usr/local/etc/php-fpm.d/

COPY ./zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

EXPOSE 9000

