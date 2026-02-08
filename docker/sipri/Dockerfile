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

# Set ServerName globally to suppress warnings
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Установка прав на writable директорию при старте контейнера
# Используем простой скрипт, который выполняется перед запуском Apache
RUN echo '#!/bin/bash\n\
set -e\n\
if [ -d /var/www/html/writable ]; then\n\
  chown -R www-data:www-data /var/www/html/writable || true\n\
  chmod -R 775 /var/www/html/writable || true\n\
fi\n\
exec docker-php-entrypoint apache2-foreground' > /usr/local/bin/docker-entrypoint.sh && \
    chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]

WORKDIR /var/www/html


