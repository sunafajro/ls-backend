FROM php:7.0-apache
RUN a2enmod rewrite
RUN apt-get update && apt-get -y install g++ git curl libcurl3-dev libfreetype6-dev libjpeg-dev libjpeg62-turbo-dev libmcrypt-dev libpng-dev libxml2-dev zlib1g-dev unzip && apt-get clean
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ --with-png-dir=/usr/include/
RUN docker-php-ext-configure bcmath
RUN docker-php-ext-install -j$(nproc) bcmath
RUN docker-php-ext-install -j$(nproc) curl
RUN docker-php-ext-install -j$(nproc) exif
RUN docker-php-ext-install -j$(nproc) gd
RUN docker-php-ext-install -j$(nproc) iconv
RUN docker-php-ext-install -j$(nproc) intl
RUN docker-php-ext-install -j$(nproc) mbstring
RUN docker-php-ext-install -j$(nproc) mcrypt
RUN docker-php-ext-install -j$(nproc) opcache
RUN docker-php-ext-install -j$(nproc) pdo
RUN docker-php-ext-install -j$(nproc) pdo_mysql
RUN docker-php-ext-install -j$(nproc) soap
RUN docker-php-ext-install -j$(nproc) zip
RUN curl -sS https://getcomposer.org/installer | php -- --filename=composer.phar --install-dir=/usr/local/bin && php /usr/local/bin/composer.phar clear-cache
WORKDIR /var/www/calc2
RUN php /usr/local/bin/composer.phar global require "fxp/composer-asset-plugin:^1.4.3"
#RUN php /usr/local/bin/composer.phar install