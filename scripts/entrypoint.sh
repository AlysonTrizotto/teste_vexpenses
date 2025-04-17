#!/bin/sh
cd /app


frankenphp php-cli artisan optimize:clear
frankenphp php-cli artisan optimize

supervisorctl reread
supervisorctl update
exec supervisord -c /etc/supervisord.conf


