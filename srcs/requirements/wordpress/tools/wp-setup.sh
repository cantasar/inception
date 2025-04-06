#!/bin/bash

if [ ! -f /var/www/html/wp-config.php ]; then

	chmod 777 /var/www/html

	cd /var/www/html

	rm -rf *

	curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar 

	chmod +x wp-cli.phar 

	mv wp-cli.phar /usr/local/bin/wp

	wp core download --allow-root

	mv /wp-config.php /var/www/html/wp-config.php

	wp core install --url=$DOMAIN_NAME/ --title=$WP_TITLE --admin_user=$WP_ADMIN_USR --admin_password=$WP_ADMIN_PWD --admin_email=$WP_ADMIN_EMAIL --skip-email --allow-root

	wp user create $WP_USR $WP_EMAIL --role=author --user_pass=$WP_PWD --allow-root

	wp theme install twentytwentyfive --activate --allow-root

fi

sed -i 's/listen = \/run\/php\/php7.4-fpm.sock/listen = 9000/g' /etc/php/7.4/fpm/pool.d/www.conf

if [ ! -d /run/php ]; then
    mkdir /run/php
fi

/usr/sbin/php-fpm7.4 -F