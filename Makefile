# Varsayılan hedef - projeyi başlat
all : up

# Projeyi başlat
up : 
# Kalıcı veri depolama dizinlerini oluştur
	mkdir -p /home/ctasar/data
	mkdir -p /home/ctasar/data/wordpress
	mkdir -p /home/ctasar/data/mariadb
# Docker Compose ile konteynerleri başlat
# -f: Compose dosyasının konumu
# -d: Arkaplanda çalıştır (detached mode)
	@docker-compose -f ./srcs/docker-compose.yml up -d

# Projeyi durdur ve konteynerleri kaldır
down : 
	@docker-compose -f ./srcs/docker-compose.yml down

# Durdurulmuş konteynerleri başlat
start : 
	@docker-compose -f ./srcs/docker-compose.yml start

# Çalışan konteynerleri durdur
stop : 
	@docker-compose -f ./srcs/docker-compose.yml stop

# Konteynerleri kaldır ve sistem temizliği yap
clean : down
# Kullanılmayan Docker nesnelerini temizle
	@docker system prune -a

# Tam temizlik - tüm konteynerleri, imajları ve verileri sil
fclean :
# Kalıcı veri dizinlerini sil
	sudo rm -rf /home/ctasar/data/wordpress
	sudo rm -rf /home/ctasar/data/mariadb
	sudo rm -rf /home/ctasar/data
# Tüm konteynerleri durdur ve sil
	docker stop $$(docker ps -qa) 2>/dev/null || true
	docker rm $$(docker ps -qa) 2>/dev/null || true
# Tüm Docker imajlarını sil
	docker rmi -f $$(docker images -qa) 2>/dev/null || true
# Tüm Docker volume'lerini sil
	docker volume rm $$(docker volume ls -q) 2>/dev/null || true
# Tüm Docker ağlarını sil
	docker network rm $$(docker network ls -q) 2>/dev/null || true

# Projeyi tamamen temizle ve yeniden başlat
re : fclean all

# Hedeflerin dosya adı olmadığını belirt
.PHONY: all up down stop start restart status clean fclean re