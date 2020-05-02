#!/bin/bash


#wget -O tools/phpunit.phar https://phar.phpunit.de/phpunit.phar
wget -O tools/phpunit.phar https://phar.phpunit.de/phpunit-7.phar
wget -O tools/phpcs.phar https://squizlabs.github.io/PHP_CodeSniffer/phpcs.phar
wget -O tools/phploc.phar https://phar.phpunit.de/phploc.phar
wget -O tools/pdepend.phar http://static.pdepend.org/php/latest/pdepend.phar
wget -O tools/phpmd.phar http://static.phpmd.org/php/latest/phpmd.phar
wget -O tools/phpcpd.phar https://phar.phpunit.de/phpcpd.phar
wget -O tools/phpdox.phar http://phpdox.de/releases/phpdox.phar

chmod +x tools/*.phar

