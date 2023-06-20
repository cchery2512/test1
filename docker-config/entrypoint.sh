#!/bin/sh
set -e

composer install

ATTEMPTS=60
until [ $ATTEMPTS -eq 0 ] || php bin/console dbal:run-sql -q "SELECT 1" >/dev/null 2>&1; do
  sleep 1
  ATTEMPTS=$((ATTEMPTS - 1))
  echo "Waiting for database to be ready..."
done

if [ $ATTEMPTS -eq 0 ]; then
  echo "The database is not up or not reachable!"
  exit 1
fi

php bin/console doctrine:migrations:migrate --no-interaction --no-ansi --allow-no-migration

if php bin/console --env=test doctrine:database:create --no-ansi --no-interaction  >/dev/null 2>&1
then
  php bin/console --env=test doctrine:schema:create --no-interaction --no-ansi
fi

php bin/console --env=test doctrine:fixtures:load --no-interaction --no-ansi
service cron start

exec "$@"
