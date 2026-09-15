#syntax=docker/dockerfile:1
FROM dunglas/frankenphp:1-php8.5

WORKDIR /app

RUN apt-get update && apt-get install -y --no-install-recommends \
        file \
        git \
    && install-php-extensions \
        @composer \
        apcu \
        intl \
        opcache \
        xdebug \
        zip \
    && rm -rf /var/lib/apt/lists/*

ENV APP_ENV=dev
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PHP_INI_SCAN_DIR=":$PHP_INI_DIR/app.conf.d"

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

COPY --link docker/php/frankenphp/conf.d/*.ini $PHP_INI_DIR/app.conf.d/
COPY --link --chmod=755 docker/php/frankenphp/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
COPY --link docker/php/frankenphp/Caddyfile /etc/frankenphp/Caddyfile

ENTRYPOINT ["docker-entrypoint"]
CMD ["frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile", "--watch"]