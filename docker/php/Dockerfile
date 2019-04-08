FROM php:7.2.9-apache-stretch AS cafet-php-server


# Install required extentions
RUN a2enmod rewrite expires
RUN apt-get update && apt-get install -y --no-install-recommends \
        libzip-dev \
        zip \
        libjpeg-dev \
        libpng-dev \

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


# Copy app
COPY app/ /var/www/html/
COPY docker/php/config.php /var/www/html/cafetapi_content/


# Prepare volumes
RUN cp -r /var/www/html/cafetapi_content /var/cafetapi_content_defaults
RUN mkdir -p /var/tmp && mkdir -p /var/log


# Copy entrypoint
COPY docker/php/docker-php-entrypoint.sh /usr/local/bin/docker-php-entrypoint
RUN chmod 775 /usr/local/bin/docker-php-entrypoint

VOLUME ["/var/log", "/var/www/html/cafetapi_content"]
