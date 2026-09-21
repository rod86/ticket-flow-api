#!/bin/sh
# Run phpcs (inside the php container) on the staged PHP files only.
# Exits 0 when no PHP file is staged.

changed_files=$(git diff --cached --name-only --diff-filter=ACMR -- '*.php')

if [ -z "$changed_files" ]; then
    printf '\033[33mThere are no files to check.\033[0m\n'
    exit 0
fi

docker compose exec -T php vendor/bin/phpcs $changed_files
