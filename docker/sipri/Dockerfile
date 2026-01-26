FROM php:8.2-apache

RUN a2enmod rewrite headers

RUN apt-get update && apt-get install -y --no-install-recommends \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) mysqli pdo_mysql intl zip gd \
    && rm -rf /var/lib/apt/lists/*

# Apache vhost
COPY docker/sipri/apache.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html


