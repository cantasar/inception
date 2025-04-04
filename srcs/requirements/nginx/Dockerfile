FROM debian:bullseye

RUN apt -y update && apt install -y nginx && apt install openssl -y

COPY ./conf/ctasar.conf /etc/nginx/sites-enabled/

COPY ./tools/certificate.sh /

EXPOSE 443

CMD ["bash", "/certificate.sh"]