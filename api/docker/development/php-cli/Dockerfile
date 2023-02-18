FROM php:8.1-cli-alpine

ENV XDEBUG_VERSION 3.1.0

RUN apk add --no-cache $PHPIZE_DEPS postgresql-dev bash coreutils unzip \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo_pgsql pdo_mysql intl \
    && pecl install xdebug-$XDEBUG_VERSION \
    && docker-php-ext-enable xdebug

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

COPY ./common/php/conf.d /usr/local/etc/php/conf.d
COPY ./development/php/conf.d /usr/local/etc/php/conf.d

COPY ./common/wait-for-it.sh /usr/local/bin/wait-for-it
RUN chmod 555 /usr/local/bin/wait-for-it

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quiet

RUN addgroup -g 1000 app && adduser -u 1000 -G app -s /bin/sh -D app

WORKDIR /app

USER app
