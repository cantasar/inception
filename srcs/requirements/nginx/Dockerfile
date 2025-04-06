# Temel imaj olarak Debian Bullseye kullanılıyor
FROM debian:bullseye

# Sistem paketleri güncelleniyor ve gerekli servisler kuruluyor
# - nginx: Web sunucusu olarak görev yapacak
# - openssl: SSL sertifikası oluşturmak için kullanılacak
RUN apt -y update && apt install -y nginx && apt install openssl -y

# NGINX site konfigürasyonu kopyalanıyor
# Bu dosya WordPress'e yönlendirme ve SSL ayarlarını içerir
COPY ./conf/ctasar.conf /etc/nginx/sites-enabled/

# SSL sertifikası oluşturma betiği kopyalanıyor
COPY ./tools/certificate.sh /

# HTTPS bağlantıları için 443 portu dışarıya açılıyor
EXPOSE 443

# Konteyner başlatıldığında SSL sertifikası oluşturulacak ve NGINX başlatılacak
CMD ["bash", "/certificate.sh"]