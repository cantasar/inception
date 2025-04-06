# Temel imaj olarak Debian Bullseye kullanılıyor
FROM debian:bullseye

# Sistem paketleri güncelleniyor ve MariaDB sunucusu kuruluyor
# MariaDB, WordPress'in veritabanı ihtiyacını karşılayacak
RUN apt-get update && apt-get install -y mariadb-server

# MariaDB yapılandırma dosyası ve veritabanı oluşturma betiği kopyalanıyor
# - 50-server.cnf: MariaDB sunucu ayarları (karakter seti, bağlantı ayarları vb.)
# - create-db.sql: WordPress veritabanı ve kullanıcısını oluşturacak SQL komutları
COPY ./conf/50-server.cnf /etc/mysql/mariadb.conf.d/
COPY ./tools/create-db.sql /

# MariaDB servisi başlatılıyor, veritabanı oluşturuluyor ve güvenlik için betik siliniyor
RUN service mariadb start && mariadb < /create-db.sql && rm -f /create-db.sql;

# Konteyner başlatıldığında MariaDB güvenli modda çalıştırılacak
CMD ["mysqld_safe"]