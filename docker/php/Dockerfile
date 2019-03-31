FROM php:7.2.9-apache-stretch AS cafet-php-server

# Install required extentions
RUN a2enmod rewrite
RUN apt-get update && apt-get install -y zlib1g-dev && docker-php-ext-install pdo pdo_mysql zip
RUN rm -rfd /var/lib/apt/lists/*

RUN mkdir -p /var/tmp && mkdir -p /var/log

# Copy needed php files
COPY docker/php/phpinfo.php /var/www/html/
COPY docker/php/php.ini /usr/local/etc/php/
COPY docker/php/extentions/ /usr/local/lib/php/extensions/no-debug-non-zts-20170718/

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
