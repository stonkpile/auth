FROM php:8.0-rc-apache
RUN docker-php-ext-install mysqli
RUN apt-get update
