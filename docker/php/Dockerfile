FROM php:7.2.9-apache-stretch AS cafet-php-server

RUN a2enmod rewrite
RUN apt-get update && apt-get install -y zlib1g-dev && docker-php-ext-install pdo pdo_mysql zip

COPY docker/php/phpinfo.php /var/www/html/
COPY docker/php/php.ini /usr/local/etc/php/
COPY docker/php/extentions/ /usr/local/lib/php/extensions/no-debug-non-zts-20170718/

COPY app/ /var/www/html/
COPY docker/php/config.php /var/www/html/cafetapi_content/

RUN cp -r /var/www/html/cafetapi_content /var/cafetapi_content_defaults
RUN mkdir -p /var/tmp && mkdir -p /var/log

COPY docker/php/docker-php-entrypoint /usr/local/bin/

VOLUME ["/var/log", "/var/www/html/cafetapi_content"]