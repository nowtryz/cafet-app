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

exec "$@"