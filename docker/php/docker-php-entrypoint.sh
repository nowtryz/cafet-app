#!/bin/sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php "$@"
fi

# look if the container need to be first install
if [ ! "$(ls -A /var/www/html/cafetapi_content)" ]; then
     cp -r /var/cafetapi_content_defaults /var/www/html/cafetapi_content
fi

exec "$@"