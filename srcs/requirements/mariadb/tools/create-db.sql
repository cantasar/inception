# WordPress için veritabanı oluştur (eğer yoksa)
CREATE DATABASE IF NOT EXISTS wordpress;

# WordPress için kullanıcı oluştur
# '%' karakteri, bu kullanıcının herhangi bir host'tan bağlanabileceğini belirtir
# Bu, Docker ağı üzerinden WordPress konteynerinin bağlanabilmesi için gerekli
CREATE USER IF NOT EXISTS 'ctasar'@'%' IDENTIFIED BY '12345';

# Oluşturulan kullanıcıya WordPress veritabanı üzerinde tüm yetkileri ver
GRANT ALL PRIVILEGES ON wordpress.* TO 'ctasar'@'%';

# Yetki değişikliklerini uygula
FLUSH PRIVILEGES;

# Root kullanıcısının şifresini güvenlik için değiştir
ALTER USER 'root'@'localhost' IDENTIFIED BY 'root12345';