#!/bin/sh
set -e
# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

export SYMFONY_ENV=$SYMFONY_ENV

if [ "$INSTALL" = true ]; then
    echo "*********** NODE VERSION ************"
    node -v
    echo "*********** NPM VERSION *************"
    npm -v
    echo "*********** PROCESS *****************"
    node -p process.versions
    echo "*********** CREATE DIRS *******************"
    mkdir -p /srv/alita/vendor
    mkdir -p /srv/alita/node_modules
    mkdir -p /srv/alita/var
    mkdir -p /srv/alita/web/upload
    mkdir -p /srv/alita/web/upload/tmp
    echo "*********** USER MAPPING *******************"
    userdel -f www-data &&\
    if getent group www-data ; then groupdel www-data; fi &&\
    groupadd -g ${GROUP_ID} www-data &&\
    useradd -l -u ${USER_ID} -g www-data www-data &&\
    install -d -m 0755 -o www-data -g www-data /home/www-data &&\
    chown --changes --silent --no-dereference --recursive --from=33:33 ${USER_ID}:${GROUP_ID} /home/www-data /var/run/php /var/lib/php/sessions
    echo "*********** ADD RIGHTS *******************"
    setfacl -dR -m u:root:rwX -m u:${USER_ID}:rwX /srv/alita/node_modules
    setfacl -dR -m u:root:rwX -m u:${USER_ID}:rwX /srv/alita/vendor
    setfacl -dR -m u:root:rwX -m u:${USER_ID}:rwX -m o::rwX /srv/alita/var
    setfacl -dR -m u:root:rwX -m u:${USER_ID}:rwX -m o::rwX /srv/alita/web
    setfacl -R -m u:root:rwX -m u:${USER_ID}:rwX /srv/alita/node_modules
    setfacl -R -m u:root:rwX -m u:${USER_ID}:rwX /srv/alita/vendor
    setfacl -R -m u:root:rwX -m u:${USER_ID}:rwX -m o::rwX /srv/alita/var
    setfacl -R -m u:root:rwX -m u:${USER_ID}:rwX -m o::rwX /srv/alita/web
    echo "*********** COMPOSER *******************"
    composer install --prefer-dist --no-suggest --no-interaction --no-scripts
    echo "*********** WEBPACK ENCORE **********"
    yarn init -y
    yarn install
    echo "*********** ASSET *******************"
    php bin/console assets:install
fi

echo "=================== UID =================="
echo  "$UID"
exec "$@"