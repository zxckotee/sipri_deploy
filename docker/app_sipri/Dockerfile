FROM php:8.2-apache

RUN a2enmod rewrite headers

RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev \
    && docker-php-ext-install -j$(nproc) mysqli pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*

COPY docker/app_sipri/apache.conf /etc/apache2/sites-available/000-default.conf

# Set ServerName globally to suppress warnings
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

WORKDIR /var/www/html


