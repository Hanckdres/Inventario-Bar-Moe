FROM php:8.1-apache

RUN rm -rf /var/www/html/*
COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

RUN docker-php-ext-install mysqli pdo pdo_mysql

EXPOSE 80