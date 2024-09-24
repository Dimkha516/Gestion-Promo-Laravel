#!/bin/sh

# Démarrer PHP-FPM
/usr/local/bin/php-fpm

# Démarrer Nginx
nginx -g 'daemon off;'


#!/bin/sh

# Démarre Nginx
# service nginx start

# Démarre PHP-FPM
# php-fpm
