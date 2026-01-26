FROM php:8.2-apache

RUN a2enmod rewrite headers

RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev \
    && docker-php-ext-install -j$(nproc) mysqli pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*

COPY docker/app_sipri/apache.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html


