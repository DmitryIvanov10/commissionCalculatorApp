FROM library/php:7.4.6-fpm-alpine
RUN apk --update add wget \
		     curl \
		     git \
		     php7 \
		     php7-curl \
		     php7-openssl \
		     php7-iconv \
		     php7-json \
		     php7-mbstring \
		     php7-phar \
		     php7-dom --repository http://nl.alpinelinux.org/alpine/edge/testing/ && rm /var/cache/apk/*

# add ext-intl
RUN apk add --no-cache icu-dev \
    && docker-php-ext-install intl

RUN chown -R www-data:www-data /var/www/html
RUN apk update && \
    apk add --no-cache --virtual dev-deps git autoconf gcc g++ make && \
    apk add --no-cache zlib-dev libzip-dev libmemcached-dev icu-dev bash

# install composer
RUN curl -sS https://getcomposer.org/installer | php \
        && mv composer.phar /usr/local/bin/ \
        && ln -s /usr/local/bin/composer.phar /usr/local/bin/composer

EXPOSE 9000
