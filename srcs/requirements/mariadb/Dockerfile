FROM debian:bullseye

RUN apt-get update && apt-get install -y mariadb-server

COPY ./conf/50-server.cnf /etc/mysql/mariadb.conf.d/
COPY ./tools/create-db.sql /

RUN service mariadb start && mariadb < /create-db.sql && rm -f /create-db.sql;

CMD ["mysqld_safe"]