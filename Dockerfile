# Build this with docker-compose or using following commands
# docker build -t mikkohei13/php-slim:0.1 .
# docker push -t mikkohei13/php-slim:VERSION-NUMBER
# (See version numbers/tags at Docker Hub: XXX)

FROM php:7.1-apache

WORKDIR /var/www/html/

# Todo: perhaps link this so could be modified? Now uses the ini file that was available during image building.
COPY config/php.ini /usr/local/etc/php/

# Add tools
RUN apt-get update && \
apt-get -y upgrade && \
apt-get -y install unzip zlib1g-dev #git

# Add php extensions / modules
RUN docker-php-ext-install zip

# Add composer
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

# Add Composer packages
#RUN composer require slim/slim "^3.0" # runs, but files are not there!?

RUN a2enmod rewrite

WORKDIR /var/www/
