
FROM dunglas/frankenphp:1-php8.5 AS base

RUN apt-get update && apt-get install -y --no-install-recommends \
        file \
        git \
    && install-php-extensions \
        apcu \
        intl \
        opcache \
        zip \
        pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer


FROM base AS development

RUN install-php-extensions xdebug

WORKDIR /app

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
COPY docker/php/app.ini $PHP_INI_DIR/conf.d/zz-app.ini
COPY docker/php/Caddyfile /etc/frankenphp/Caddyfile

CMD ["frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile", "--watch"]
