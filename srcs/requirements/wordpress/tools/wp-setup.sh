#!/bin/bash

# WordPress kurulu değilse kurulumu gerçekleştir
if [ ! -f /var/www/html/wp-config.php ]; then

    # WordPress dizinine tam yetki ver
    chmod 777 /var/www/html

    # WordPress dizinine geç
    cd /var/www/html

    # Mevcut dosyaları temizle
    rm -rf *

    # WP-CLI aracını indir
    curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar 

    # WP-CLI'ye çalıştırma izni ver
    chmod +x wp-cli.phar 

    # WP-CLI'yi sistem yoluna taşı
    mv wp-cli.phar /usr/local/bin/wp

    # WordPress çekirdek dosyalarını indir
    wp core download --allow-root

    # WordPress yapılandırma dosyasını kopyala
    mv /wp-config.php /var/www/html/wp-config.php

    # WordPress'i kur ve yönetici hesabı oluştur
    # --url: Site adresi
    # --title: Site başlığı
    # --admin_user: Yönetici kullanıcı adı
    # --admin_password: Yönetici şifresi
    # --admin_email: Yönetici e-posta adresi
    wp core install --url=$DOMAIN_NAME/ --title=$WP_TITLE \
        --admin_user=$WP_ADMIN_USR --admin_password=$WP_ADMIN_PWD \
        --admin_email=$WP_ADMIN_EMAIL --skip-email --allow-root

    # İkinci bir kullanıcı oluştur (yazar rolünde)
    wp user create $WP_USR $WP_EMAIL --role=author \
        --user_pass=$WP_PWD --allow-root

    # Twenty Twenty-Five temasını kur ve etkinleştir
    wp theme install twentytwentyfive --activate --allow-root

fi

# PHP-FPM yapılandırmasını güncelle
# Unix soketi yerine TCP/IP bağlantısı kullan (port 9000)
sed -i 's/listen = \/run\/php\/php7.4-fpm.sock/listen = 9000/g' \
    /etc/php/7.4/fpm/pool.d/www.conf

# PHP-FPM çalışma dizinini oluştur
if [ ! -d /run/php ]; then
    mkdir /run/php
fi

# PHP-FPM'i ön planda başlat
/usr/sbin/php-fpm7.4 -F