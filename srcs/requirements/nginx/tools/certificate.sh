#!/bin/bash

# OpenSSL kullanarak kendinden imzalı bir SSL sertifikası oluştur
# -x509: X.509 sertifika formatını kullan
# -nodes: Şifresiz özel anahtar oluştur
# -days 365: Sertifikanın geçerlilik süresi (1 yıl)
# -keyout: Özel anahtarın kaydedileceği konum
# -out: Sertifikanın kaydedileceği konum
# -subj: Sertifika bilgileri (Ülke, Şehir, Organizasyon, Alan adı)
openssl req -x509 -nodes -days 365 -keyout /etc/ssl/private/ssl.key -out /etc/ssl/certs/ssl.crt -subj "/C=TR/ST=ISTANBUL/L=SARIYER/O=42/CN=ctasar.42.fr";

# NGINX'i ön planda çalıştır (Docker konteynerinin kapanmaması için)
nginx -g "daemon off;"