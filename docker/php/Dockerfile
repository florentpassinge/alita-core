ARG PHP_VERSION_MAJOR
ARG PHP_VERSION_MINOR
ARG PHP_VERSION_HUB_REPOSITORY=${PHP_VERSION_MAJOR}"."${PHP_VERSION_MINOR}
FROM florentpassinge/alita-php:latest
ARG PHP_VERSION_HUB_REPOSITORY

# ENV NEED ARG AGAIN
ARG PHP_VERSION_MAJOR
ARG PHP_VERSION_MINOR
ENV PHP_VERSION_MAJOR=$PHP_VERSION_MAJOR
ENV PHP_VERSION_MINOR=$PHP_VERSION_MINOR
ENV PHP_FPM_PID="/run/php/php${PHP_VERSION_MAJOR}-fpm.pid"
ENV PHP_FPM_INCLUDE_CONF="/etc/php/${PHP_VERSION_MAJOR}/fpm/pool.d/*.conf"

ARG USER_ID
ARG GROUP_ID
ENV USER_ID=$USER_ID
ENV GROUP_ID=$GROUP_ID

# TO CHECK ENV
RUN echo "**** PHP_VERSION_HUB_REPOSITORY is $PHP_VERSION_HUB_REPOSITORY ****"
RUN echo "**** USER_ID is $USER_ID ****"
RUN echo "**** GROUP_ID is $GROUP_ID ****"
RUN echo "**** PHP_VERSION is $PHP_VERSION *****"
RUN echo "**** PHP_VERSION_MAJOR is $PHP_VERSION_MAJOR *****"
RUN echo "**** PHP_VERSION_MINOR is $PHP_VERSION_MINOR *****"

RUN mv /etc/php/${PHP_VERSION_MAJOR}/fpm/pool.d/www.conf /etc/php/${PHP_VERSION_MAJOR}/fpm/pool.d/www.conf.bak
ADD ./docker/php/conf/www.conf /etc/php/${PHP_VERSION_MAJOR}/fpm/pool.d/www.conf

RUN mv /etc/php/${PHP_VERSION_MAJOR}/fpm/php-fpm.conf /etc/php/${PHP_VERSION_MAJOR}/fpm/php-fpm.conf.bak
ADD ./docker/php/conf/php-fpm.conf /etc/php/${PHP_VERSION_MAJOR}/fpm/php-fpm.conf

RUN mv /etc/php/${PHP_VERSION_MAJOR}/fpm/php.ini /etc/php/${PHP_VERSION_MAJOR}/fpm/php.ini.bak
ADD ./docker/php/conf/php.ini /etc/php/${PHP_VERSION_MAJOR}/fpm/php.ini

RUN mv /etc/php/${PHP_VERSION_MAJOR}/cli/php.ini /etc/php/${PHP_VERSION_MAJOR}/cli/php.ini.bak
ADD ./docker/php/conf/php-cli.dev.ini /etc/php/${PHP_VERSION_MAJOR}/cli/php.ini

ADD ./docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["/usr/local/bin/docker-entrypoint"]

CMD php-fpm${PHP_VERSION_MAJOR}

ENV COMPOSER_ALLOW_SUPERUSER 1
RUN composer global require "hirak/prestissimo:^0.3" --prefer-dist --no-progress --no-suggest --classmap-authoritative
RUN composer global require fxp/composer-asset-plugin
