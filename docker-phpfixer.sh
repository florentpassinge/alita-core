#!/bin/sh
docker-compose exec -T php php /srv/alita/build/tools/php-cs-fixer.phar --config=/srv/alita/build/tools/phpcs/.php_cs "$@"