#!/bin/bash
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php "$@"
fi

# copy default content application
cp -r -T -n /var/cafetapi_content_defaults/. /var/www/html/cafetapi_content

# Config file for sSMTP sendmail
cat > /etc/ssmtp/ssmtp.conf << EOL
root=cafet@${SERVER_DOMAIN}
mailhub=${MAILHUB}
hostname=${SERVER_DOMAIN}
FromLineOverride=YES
EOL

# Enble php to use error.log
touch /var/www/html/cafetapi_content/error.log
chown www-data /var/www/html/cafetapi_content/error.log
ln -s /var/www/html/cafetapi_content/error.log /var/www/html/error.log

exec "$@"