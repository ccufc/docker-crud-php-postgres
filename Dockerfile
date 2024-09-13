FROM php:8.1-apache

RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo_pgsql pgsql

COPY src/ /var/www/html

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
RUN service apache2 restart

EXPOSE 80
