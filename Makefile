all : up

up : 
	mkdir -p /home/ctasar/data
	mkdir -p /home/ctasar/data/wordpress
	mkdir -p /home/ctasar/data/mariadb
	@docker-compose -f ./srcs/docker-compose.yml up -d

down : 
	@docker-compose -f ./srcs/docker-compose.yml down

start : 
	@docker-compose -f ./srcs/docker-compose.yml start

stop : 
	@docker-compose -f ./srcs/docker-compose.yml stop

clean : down
	@docker system prune -a

fclean :
	sudo rm -rf /home/ctasar/data/wordpress
	sudo rm -rf /home/ctasar/data/mariadb
	sudo rm -rf /home/ctasar/data
	docker stop $$(docker ps -qa) 2>/dev/null || true
	docker rm $$(docker ps -qa) 2>/dev/null || true
	docker rmi -f $$(docker images -qa) 2>/dev/null || true
	docker volume rm $$(docker volume ls -q) 2>/dev/null || true
	docker network rm $$(docker network ls -q) 2>/dev/null || true

re : fclean all

.PHONY: all up down stop start restart status clean fclean re