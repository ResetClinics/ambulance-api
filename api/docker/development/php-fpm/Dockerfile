FROM php:8.1-fpm-alpine

ENV XDEBUG_VERSION 3.1.0

RUN apk add --no-cache $PHPIZE_DEPS postgresql-dev bash coreutils unzip \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo_pgsql pdo_mysql intl \
    && pecl install xdebug-$XDEBUG_VERSION \
    && docker-php-ext-enable xdebug


RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

COPY ./common/php/conf.d /usr/local/etc/php/conf.d
COPY ./common/php/php-fpm.d /usr/local/etc/php-fpm.d
COPY ./development/php/conf.d /usr/local/etc/php/conf.d
COPY ./development/php-fpm/conf.d /usr/local/etc/php/conf.d

WORKDIR /app

COPY ./development/php-fpm/entrypoint.sh /usr/local/bin/docker-php-entrypoint
RUN chmod +x /usr/local/bin/docker-php-entrypoint

HEALTHCHECK --interval=5s --timeout=3s --start-period=1s \
    CMD REDIRECT_STATUS=true SCRIPT_NAME=/ping SCRIPT_FILENAME=/ping REQUEST_METHOD=GET \
    cgi-fcgi -bind -connect 127.0.0.1:9000 || exit 1
