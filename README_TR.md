# Inception

Bu proje, sistem yönetimi ve DevOps alanında Docker ve sanallaştırma becerilerini geliştirmek amacıyla oluşturulmuş bir sistem ortamıdır. Projede LEMP stack (Linux, NGINX, MariaDB, PHP) kullanılarak konteyner tabanlı bir web uygulaması altyapısı kurulmuştur.

Docker Compose kullanılarak üç temel servis (NGINX, WordPress, MariaDB) birbirine bağlanmış, güvenli ve izole bir çalışma ortamı oluşturulmuştur.

## Proje Mimarisi
Projenin ana bileşenleri:

1. **NGINX**: Ters proxy sunucusu olarak görev yapar, TLS/SSL terminasyonu sağlar
2. **WordPress**: PHP-FPM destekli WordPress uygulaması
3. **MariaDB**: WordPress veritabanı için ilişkisel veritabanı sistemi

Bu servisler Docker konteynerleri içinde izole edilmiş ve birbirleriyle Docker ağı üzerinden iletişim kurabilecek şekilde yapılandırılmıştır.

## Kurulum ve Çalıştırma

```bash
# Projeyi çalıştırmak için
make

# Projeyi durdurmak için
make down

# Tüm konteynerleri ve volumeleri silmek için
make clean

# Tüm konteynerleri, imajları ve volumeleri silmek için 
make fclean

# Projeyi yeniden başlatmak için
make re
```

## Servis Detayları

### NGINX Servisi
NGINX servisi, dış dünyadan gelen istekleri karşılayan bir ters proxy sunucusu olarak görev yapar. 443 portunu dinleyerek HTTPS trafiğini kabul eder ve WordPress uygulamasına yönlendirir.

#### Dockerfile İncelemesi
```dockerfile
FROM debian:bullseye

RUN apt -y update && apt install -y nginx && apt install openssl -y

COPY ./conf/ctasar.conf /etc/nginx/sites-enabled/

COPY ./tools/certificate.sh /

EXPOSE 443

CMD ["bash", "/certificate.sh"]
```

Burada yapılan işlemler:
1. Debian Bullseye temel imajı kullanılır
2. NGINX ve OpenSSL paketleri kurulur
3. NGINX site yapılandırması kopyalanır
4. SSL sertifikası oluşturma betiği kopyalanır
5. 443 portu dışarıya açılır
6. Konteyner başladığında SSL sertifikası oluşturma betiği çalıştırılır

#### NGINX Yapılandırması (`ctasar.conf`)
```nginx
server {
    listen 443 ssl;
    listen [::]:443 ssl;

    server_name ctasar.42.fr;

    ssl_certificate     /etc/ssl/certs/ssl.crt;
    ssl_certificate_key /etc/ssl/private/ssl.key;

    ssl_protocols       TLSv1.3;

    index index.php;
    root  /var/www/html;

    location ~ [^/]\.php(/|$) {
        try_files         $uri =404;
        fastcgi_pass      wordpress:9000;
        include           fastcgi_params;
        fastcgi_param     SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

Bu yapılandırma dosyası:
- 443 portunu SSL ile dinler
- SSL sertifika ve anahtar dosyalarının konumlarını belirler
- TLSv1.3 protokolünü kullanır
- PHP dosyaları için istekleri WordPress konteynerine (9000 portu üzerinden) yönlendirir
- WordPress kök dizini olarak /var/www/html dizinini kullanır

#### SSL Sertifika Oluşturma (`certificate.sh`)
```bash
#!/bin/bash

openssl req -x509 -nodes -days 365 -keyout /etc/ssl/private/ssl.key -out /etc/ssl/certs/ssl.crt -subj "/C=TR/ST=ISTANBUL/L=SARIYER/O=42/CN=ctasar.42.fr";

nginx -g "daemon off;"
```

Bu betik:
1. OpenSSL kullanarak kendinden imzalı bir SSL sertifikası oluşturur (365 gün geçerli)
2. NGINX'i ön planda çalıştırır (daemon off modu)

### WordPress Servisi
WordPress servisi, PHP 7.4 ve FPM kullanarak WordPress uygulamasını çalıştırır.

#### Dockerfile İncelemesi
```dockerfile
FROM debian:bullseye

RUN apt update -y && \
    apt install -y \
    php7.4-fpm \
    php7.4-mysql \
    curl

COPY ./conf/wp-config.php /
COPY ./tools/wp-setup.sh /

RUN chmod +x /wp-setup.sh

CMD ["bash", "/wp-setup.sh"]
```

Burada yapılan işlemler:
1. Debian Bullseye temel imajı kullanılır
2. PHP FPM, PHP MySQL eklentisi ve curl kurulur
3. WordPress yapılandırma dosyası ve kurulum betiği kopyalanır
4. Kurulum betiğine çalıştırma izni verilir
5. Konteyner başladığında kurulum betiği çalıştırılır

#### WordPress Kurulum Betiği (`wp-setup.sh`)
```bash
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
```

Bu betik şunları yapar:
1. WordPress kurulu değilse:
   - /var/www/html dizinine gerekli izinleri verir
   - WP-CLI (WordPress Command Line Interface) indirir
   - WordPress çekirdek dosyalarını indirir
   - WordPress yapılandırma dosyasını (/wp-config.php) kopyalar
   - WordPress kurulumunu tamamlar ve admin kullanıcısı oluşturur
   - İkinci bir kullanıcı (yazar rolünde) oluşturur
   - Varsayılan tema olarak Twenty Twenty-Five'ı kurar ve etkinleştirir
2. PHP-FPM yapılandırmasını günceller (Unix soketi yerine 9000 portunu dinlemek için)
3. PHP-FPM için gerekli dizini oluşturur
4. PHP-FPM'i ön planda çalıştırır

#### WordPress Yapılandırması (`wp-config.php`)
WordPress yapılandırma dosyası veritabanı bağlantısı, karakter seti, tablo öneki ve güvenlik anahtarları gibi temel ayarları içerir:

```php
// Veritabanı Ayarları
define( 'DB_NAME', 'wordpress' );
define( 'DB_USER', 'ctasar' );
define( 'DB_PASSWORD', '12345' );
define( 'DB_HOST', 'mariadb' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

// Güvenlik Anahtarları
// ... (Güvenlik anahtarları burada yer alır)

// Tablo Öneki
$table_prefix = 'wp_';

// Hata Ayıklama Modu
define( 'WP_DEBUG', false );
```

Bu yapılandırma dosyası:
- MariaDB servisine 'mariadb' host adıyla bağlanır
- Veritabanı kullanıcı bilgilerini içerir
- WordPress'in çalışması için gerekli güvenlik anahtarlarını içerir

### MariaDB Servisi
MariaDB servisi, WordPress için veritabanı sunucusu olarak görev yapar.

#### Dockerfile İncelemesi
```dockerfile
FROM debian:bullseye

RUN apt-get update && apt-get install -y mariadb-server

COPY ./conf/50-server.cnf /etc/mysql/mariadb.conf.d/
COPY ./tools/create-db.sql /

RUN service mariadb start && mariadb < /create-db.sql && rm -f /create-db.sql;

CMD ["mysqld_safe"]
```

Burada yapılan işlemler:
1. Debian Bullseye temel imajı kullanılır
2. MariaDB sunucusu kurulur  
3. MariaDB yapılandırma dosyası ve veritabanı oluşturma betiği kopyalanır
4. MariaDB servisi başlatılır ve veritabanı oluşturma betiği çalıştırılır
5. Güvenlik için betik dosyası silinir
6. Konteyner başladığında MariaDB sunucusu çalıştırılır (mysqld_safe ile)

#### MariaDB Yapılandırması (`50-server.cnf`)
MariaDB sunucusu için çeşitli yapılandırma ayarlarını içerir:

```cnf
[mysqld]
user                    = mysql
pid-file                = /run/mysqld/mysqld.pid
socket                  = /run/mysqld/mysqld.sock
port                    = 3306
basedir                 = /usr
datadir                 = /var/lib/mysql
tmpdir                  = /tmp
lc-messages-dir         = /usr/share/mysql
bind-address            = 0.0.0.0
query_cache_size        = 16M
log_error               = /var/log/mysql/error.log
expire_logs_days        = 10
character-set-server    = utf8mb4
collation-server        = utf8mb4_general_ci
```

Özellikle önemli ayarlar:
- `bind-address = 0.0.0.0` - MariaDB'nin tüm IP adreslerinden gelen bağlantıları kabul etmesini sağlar
- `character-set-server = utf8mb4` - UTF-8 karakter seti kullanır (emoji desteği dahil)
- `query_cache_size = 16M` - Sorgu önbelleği boyutunu belirler

#### Veritabanı Oluşturma Betiği (`create-db.sql`)
```sql
CREATE DATABASE IF NOT EXISTS wordpress;
CREATE USER IF NOT EXISTS 'ctasar'@'%' IDENTIFIED BY '12345';
GRANT ALL PRIVILEGES ON wordpress.* TO 'ctasar'@'%';
FLUSH PRIVILEGES;
ALTER USER 'root'@'localhost' IDENTIFIED BY 'root12345';
```

Bu SQL betiği:
1. WordPress veritabanını oluşturur (yoksa)
2. 'ctasar' kullanıcısını oluşturur ve şifresini ayarlar
3. Bu kullanıcıya WordPress veritabanı üzerinde tüm yetkileri verir
4. Yetkileri yeniden yükler (flush privileges)
5. Root kullanıcısına şifre ataması yapar

## Docker Compose Yapılandırması
`docker-compose.yml` dosyası üç servisi (NGINX, WordPress, MariaDB) bir araya getirir ve aralarındaki ilişkileri tanımlar:

```yaml
version: "3.8"

services:
  nginx:
    container_name: nginx
    build: ./requirements/nginx/.
    ports:
      - "443:443"
    depends_on:
      - wordpress
    volumes:
      - wordpress:/var/www/html
    networks:
      - inception
    env_file:
      - .env
    restart: always

  wordpress:
    container_name: wordpress
    build: ./requirements/wordpress/.
    depends_on:
      - mariadb
    volumes:
      - wordpress:/var/www/html
    env_file:
      - .env
    networks:
      - inception
    restart: always

  mariadb:
    container_name: mariadb
    build: ./requirements/mariadb/.
    volumes:
      - mariadb:/var/lib/mysql
    env_file:
      - .env
    networks:
      - inception
    restart: always

volumes:
  wordpress:
    name: wordpress
    driver: local
    driver_opts:
      device: ../data/wordpress
      o: bind
      type: none
  mariadb:
    name: mariadb
    driver: local
    driver_opts:
      device: ../data/mariadb
      o: bind
      type: none

networks:
  inception:
    name: inception
```

Bu yapılandırma:
1. Üç servis tanımlar (nginx, wordpress, mariadb)
2. Servisler arasındaki bağımlılıkları belirler (nginx → wordpress → mariadb)
3. İki kalıcı veri hacmi tanımlar (wordpress, mariadb)
4. Özel bir ağ oluşturur (inception)
5. Servislerin yeniden başlatılma politikasını belirler (always)

## Çevre Değişkenleri (.env)
Proje `.env` dosyası üzerinden çeşitli yapılandırma değerlerini alır:

```properties
DOMAIN_NAME=ctasar.42.fr

WP_TITLE=inception

WP_ADMIN_USR=ctasar
WP_ADMIN_PWD=1234
WP_ADMIN_EMAIL=ctasar@42.fr

WP_USR=editor
WP_EMAIL=editor@gmail.com
WP_PWD=123
```

Bu değişkenler:
1. WordPress site adı ve domainini belirler
2. WordPress admin kullanıcısı bilgilerini tanımlar
3. İkinci bir WordPress kullanıcısı bilgilerini tanımlar

## Nasıl Çalışır?

1. `make` komutu çalıştırıldığında Docker Compose ile konteynerler oluşturulur
2. İlk olarak MariaDB konteyneri başlar ve veritabanını hazırlar
3. Ardından WordPress konteyneri başlar, veritabanına bağlanır ve WordPress kurulumunu yapar
4. Son olarak NGINX konteyneri başlar ve WordPress'e dış erişim sağlar
5. Tüm servisler "inception" adlı Docker ağı üzerinden birbiriyle iletişim kurar
6. WordPress ve MariaDB verileri kalıcı hacimler üzerinde saklanır

## Güvenlik Özellikleri

1. NGINX yalnızca HTTPS (443 portu) üzerinden erişilebilir
2. SSL/TLS 1.3 protokolü kullanılır
3. WordPress veritabanı yapılandırması güvenli bir şekilde yapılmıştır
4. MariaDB root şifresi değiştirilmiştir

## Teknik Detaylar

- Docker ve Docker Compose kullanılarak konteynerleştirilmiş servisler
- Debian Bullseye temel imajları
- NGINX web sunucusu ve ters proxy
- PHP 7.4 ve PHP-FPM
- MariaDB veritabanı sunucusu
- WordPress CMS
- TLS/SSL sertifikası ile güvenli bağlantı
- Kalıcı veri depolama için Docker hacimleri
- Servisler arası iletişim için özel Docker ağı