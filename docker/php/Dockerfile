## Base configuration of the php image
FROM php:7.2.9-apache-stretch AS base-php


# Install required extentions
RUN a2enmod rewrite expires
RUN apt-get update && apt-get install -y --no-install-recommends \
        libzip-dev \
        zip \
        libjpeg-dev \
        libpng-dev

RUN docker-php-ext-install \
        gd \
        pdo \
        pdo_mysql \
        zip \
        opcache

RUN rm -rfd /var/lib/apt/lists/*


# PHP configurations
RUN docker-php-ext-configure zip --with-libzip
RUN docker-php-ext-configure gd --with-png-dir=/usr --with-jpeg-dir=/usr

# set recommended PHP.ini settings
# see https://secure.php.net/manual/en/opcache.installation.php
RUN { \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.interned_strings_buffer=8'; \
        echo 'opcache.max_accelerated_files=4000'; \
        echo 'opcache.revalidate_freq=60'; \
        echo 'opcache.fast_shutdown=1'; \
        echo 'opcache.enable_cli=1'; \
} > /usr/local/etc/php/conf.d/opcache-recommended.ini


# Copy needed php files
COPY docker/php/phpinfo.php /var/www/html/


# Copy entrypoint
COPY docker/php/docker-php-entrypoint.sh /usr/local/bin/docker-php-entrypoint
RUN chmod 775 /usr/local/bin/docker-php-entrypoint

# Prepare volumes
RUN mkdir -p /var/tmp && mkdir -p /var/log && mkdir -p /var/cafetapi_content_defaults


VOLUME ["/var/log", "/var/cafetapi_content_defaults"]


## Js build
FROM node:stretch-slim AS js-build 

# copy project descrition and install it
COPY ./package.json yarn.lock /var/app/
RUN cd /var/app/ && yarn install

# copy application and compile it
COPY server/ /var/app/server/
COPY web-client/ /var/app/web-client/
COPY webpack.config.babel.js .babelrc /var/app/
RUN cd /var/app/ && yarn prod:build


## Final image
FROM base-php AS cafet-php-server

# Copy built server
COPY --chown=www-data:www-data --from=js-build /var/app/server/ /var/www/html/
COPY docker/php/config.php /var/www/html/cafetapi_content/
RUN cp -r /var/www/html/cafetapi_content /var/cafetapi_content_defaults

