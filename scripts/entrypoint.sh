#!/bin/bash
set -euo pipefail

APP_DIR=/var/www/portfolio

function permissions() {
    chown -R www-data:www-data "${APP_DIR}/storage" "${APP_DIR}/bootstrap/cache"
    chmod -R 775 "${APP_DIR}/storage" "${APP_DIR}/bootstrap/cache"
}

function prepareStorage() {
    php artisan storage:link --force
}

function optimizeApp() {
    php artisan optimize:clear
    php artisan optimize
}

function serve() {
    php-fpm --nodaemonize &
    exec nginx -g 'daemon off;'
}

function main() {
    permissions
    prepareStorage
    optimizeApp

    case "${1:-serve}" in
        serve)
            serve
            ;;
        *)
            exec "$@"
            ;;
    esac
}

main "$@"
