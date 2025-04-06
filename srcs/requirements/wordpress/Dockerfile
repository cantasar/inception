# Temel imaj olarak Debian Bullseye kullanılıyor
FROM debian:bullseye

# Sistem paketleri güncelleniyor ve gerekli servisler kuruluyor
# - php7.4-fpm: PHP FastCGI Process Manager - WordPress'in PHP kodlarını çalıştırmak için
# - php7.4-mysql: PHP'nin MySQL/MariaDB ile iletişim kurabilmesi için gerekli eklenti
# - curl: WP-CLI'yi indirmek için kullanılacak
RUN apt update -y && \
    apt install -y \
    php7.4-fpm \
    php7.4-mysql \
    curl

# WordPress yapılandırma dosyası ve kurulum betiği kopyalanıyor
# - wp-config.php: WordPress'in veritabanı bağlantısı ve diğer ayarları
# - wp-setup.sh: WordPress'i otomatik olarak kuracak ve yapılandıracak betik
COPY ./conf/wp-config.php /
COPY ./tools/wp-setup.sh /

# Kurulum betiğine çalıştırma izni veriliyor
RUN chmod +x /wp-setup.sh

# Konteyner başlatıldığında WordPress kurulumu yapılacak ve PHP-FPM başlatılacak
CMD ["bash", "/wp-setup.sh"]