#!/usr/bin/env bash
set -e

cd /var/www/html

if [ -z "${APP_KEY:-}" ]; then
    export APP_KEY
    APP_KEY="$(php -r 'echo "base64:".base64_encode(random_bytes(32));')"
fi

mkdir -p storage/app/public storage/framework/cache storage/framework/sessions storage/framework/testing storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

if [ "${DB_CONNECTION:-}" = "mysql" ] && [ -n "${DB_HOST:-}" ]; then
    until mysqladmin ping -h"${DB_HOST}" -P"${DB_PORT:-3306}" -u"${DB_USERNAME}" -p"${DB_PASSWORD}" --silent; do
        sleep 2
    done
fi

php artisan storage:link --force >/dev/null 2>&1 || true
php artisan optimize:clear

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    php artisan migrate --force
fi

exec "$@"
