#!/bin/sh
cd /app

# Aguarda o MySQL responder na porta 3306
until nc -z -v -w30 mysql-db 3306
do
  echo "Aguardando MySQL estar disponível..."
  sleep 5
done

echo "MySQL está pronto! Iniciando aplicação..."

frankenphp php-cli artisan migrate

frankenphp php-cli artisan optimize:clear
frankenphp php-cli artisan optimize

supervisorctl reread
supervisorctl update
exec supervisord -c /etc/supervisord.conf


