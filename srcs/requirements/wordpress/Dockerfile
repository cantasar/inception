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