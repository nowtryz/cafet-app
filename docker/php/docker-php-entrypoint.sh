#!/bin/sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php "$@"
fi

# look if the container need to be first install
cp -r -T -n /var/cafetapi_content_defaults/. /var/www/html/cafetapi_content

echo "127.0.0.1 $(hostname) localhost localhost.localdomain" >> /etc/hosts
service sendmail restart

exec "$@"