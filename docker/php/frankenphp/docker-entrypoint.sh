#!/bin/sh
set -e

if [ -f composer.json ] && [ -z "$(ls -A 'vendor/' 2>/dev/null)" ]; then
	composer install --prefer-dist --no-progress
fi

exec docker-php-entrypoint "$@"