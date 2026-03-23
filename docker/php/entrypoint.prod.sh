#!/bin/sh
set -e

if [ "$(id -u)" = "0" ]; then
  mkdir -p /srv/var/cache/prod/pools /srv/var/log /srv/public/media
  chown -R www-data:www-data /srv/var /srv/public/media
  chmod -R u+rwX,g+rwX /srv/var /srv/public/media
  exec su -s /bin/sh -c "php-fpm -F" www-data
fi

exec php-fpm -F
