FROM debian:bullseye

RUN apt update -y && apt install php-fpm php-mysql curl -y

COPY ./conf/wp-config.php /
COPY ./tools/wp-setup.sh /

RUN chmod +x /script.sh

CMD ["bash", "/wp-setup.sh"]