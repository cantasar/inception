# Inception

This project is a system environment created to develop skills in system administration and DevOps, focusing on Docker and virtualization. The project establishes a container-based web application infrastructure using the LEMP stack (Linux, NGINX, MariaDB, PHP).

Using Docker Compose, three main services (NGINX, WordPress, MariaDB) are interconnected, creating a secure and isolated working environment.

## Project Architecture
The main components of the project:

1. **NGINX**: Acts as a reverse proxy server and provides TLS/SSL termination
2. **WordPress**: PHP-FPM supported WordPress application
3. **MariaDB**: Relational database system for the WordPress database

These services are isolated within Docker containers and configured to communicate with each other via a Docker network.

## Setup and Execution

```bash
# To run the project
make

# To stop the project
make down

# To remove all containers and volumes
make clean

# To remove all containers, images, and volumes
make fclean

# To restart the project
make re
```

## Service Details

### NGINX Service
The NGINX service acts as a reverse proxy server handling incoming requests from the outside world. It listens on port 443 to accept HTTPS traffic and forwards it to the WordPress application.

#### Dockerfile Overview
```dockerfile
FROM debian:bullseye

RUN apt -y update && apt install -y nginx && apt install openssl -y

COPY ./conf/ctasar.conf /etc/nginx/sites-enabled/

COPY ./tools/certificate.sh /

EXPOSE 443

CMD ["bash", "/certificate.sh"]
```

Operations performed here:
1. Uses the Debian Bullseye base image
2. Installs NGINX and OpenSSL packages
3. Copies the NGINX site configuration
4. Copies the SSL certificate generation script
5. Exposes port 443
6. Runs the SSL certificate generation script when the container starts

#### NGINX Configuration (`ctasar.conf`)
```nginx
server {
    listen 443 ssl;
    listen [::]:443 ssl;

    server_name ctasar.42.fr;

    ssl_certificate     /etc/ssl/certs/ssl.crt;
    ssl_certificate_key /etc/ssl/private/ssl.key;

    ssl_protocols       TLSv1.3;

    index index.php;
    root  /var/www/html;

    location ~ [^/]\.php(/|$) {
        try_files         $uri =404;
        fastcgi_pass      wordpress:9000;
        include           fastcgi_params;
        fastcgi_param     SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

This configuration file:
- Listens on port 443 with SSL
- Specifies the locations of the SSL certificate and key files
- Uses the TLSv1.3 protocol
- Forwards requests for PHP files to the WordPress container (via port 9000)
- Uses `/var/www/html` as the WordPress root directory

#### SSL Certificate Generation (`certificate.sh`)
```bash
#!/bin/bash

openssl req -x509 -nodes -days 365 -keyout /etc/ssl/private/ssl.key -out /etc/ssl/certs/ssl.crt -subj "/C=TR/ST=ISTANBUL/L=SARIYER/O=42/CN=ctasar.42.fr";

nginx -g "daemon off;"
```

This script:
1. Generates a self-signed SSL certificate using OpenSSL (valid for 365 days)
2. Runs NGINX in the foreground (daemon off mode)

### WordPress Service
The WordPress service runs the WordPress application using PHP 7.4 and FPM.

#### Dockerfile Overview
```dockerfile
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
```

Operations performed here:
1. Uses the Debian Bullseye base image
2. Installs PHP FPM, PHP MySQL extension, and curl
3. Copies the WordPress configuration file and setup script
4. Grants execution permission to the setup script
5. Runs the setup script when the container starts

#### WordPress Setup Script (`wp-setup.sh`)
```bash
#!/bin/bash

if [ ! -f /var/www/html/wp-config.php ]; then

	chmod 777 /var/www/html

	cd /var/www/html

	rm -rf *

	curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar 

	chmod +x wp-cli.phar 

	mv wp-cli.phar /usr/local/bin/wp

	wp core download --allow-root

	mv /wp-config.php /var/www/html/wp-config.php

	wp core install --url=$DOMAIN_NAME/ --title=$WP_TITLE --admin_user=$WP_ADMIN_USR --admin_password=$WP_ADMIN_PWD --admin_email=$WP_ADMIN_EMAIL --skip-email --allow-root

	wp user create $WP_USR $WP_EMAIL --role=author --user_pass=$WP_PWD --allow-root

	wp theme install twentytwentyfive --activate --allow-root

fi

sed -i 's/listen = \/run\/php\/php7.4-fpm.sock/listen = 9000/g' /etc/php/7.4/fpm/pool.d/www.conf

if [ ! -d /run/php ]; then
    mkdir /run/php
fi

/usr/sbin/php-fpm7.4 -F
```

This script performs the following:
1. If WordPress is not already installed:
   - Grants necessary permissions to `/var/www/html`
   - Downloads WP-CLI (WordPress Command Line Interface)
   - Downloads WordPress core files
   - Copies the WordPress configuration file (`/wp-config.php`)
   - Completes the WordPress installation and creates an admin user
   - Creates a second user (with author role)
   - Installs and activates the default theme Twenty Twenty-Five
2. Updates PHP-FPM configuration to listen on port 9000 instead of a Unix socket
3. Creates the necessary directory for PHP-FPM
4. Runs PHP-FPM in the foreground

#### WordPress Configuration (`wp-config.php`)
The WordPress configuration file contains essential settings such as database connection, character set, table prefix, and security keys:

```php
// Database Settings
define( 'DB_NAME', 'wordpress' );
define( 'DB_USER', 'ctasar' );
define( 'DB_PASSWORD', '12345' );
define( 'DB_HOST', 'mariadb' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

// Security Keys
// ... (Security keys are defined here)

// Table Prefix
$table_prefix = 'wp_';

// Debug Mode
define( 'WP_DEBUG', false );
```

This configuration file:
- Connects to the MariaDB service using the hostname `mariadb`
- Contains database user credentials
- Includes security keys required for WordPress operation

### MariaDB Service
The MariaDB service acts as the database server for WordPress.

#### Dockerfile Overview
```dockerfile
FROM debian:bullseye

RUN apt-get update && apt-get install -y mariadb-server

COPY ./conf/50-server.cnf /etc/mysql/mariadb.conf.d/
COPY ./tools/create-db.sql /

RUN service mariadb start && mariadb < /create-db.sql && rm -f /create-db.sql;

CMD ["mysqld_safe"]
```

Operations performed here:
1. Uses the Debian Bullseye base image
2. Installs the MariaDB server
3. Copies the MariaDB configuration file and database creation script
4. Starts the MariaDB service and runs the database creation script
5. Deletes the script for security
6. Runs the MariaDB server (`mysqld_safe`) when the container starts

#### MariaDB Configuration (`50-server.cnf`)
The MariaDB server configuration file contains various settings:

```cnf
[mysqld]
user                    = mysql
pid-file                = /run/mysqld/mysqld.pid
socket                  = /run/mysqld/mysqld.sock
port                    = 3306
basedir                 = /usr
datadir                 = /var/lib/mysql
tmpdir                  = /tmp
lc-messages-dir         = /usr/share/mysql
bind-address            = 0.0.0.0
query_cache_size        = 16M
log_error               = /var/log/mysql/error.log
expire_logs_days        = 10
character-set-server    = utf8mb4
collation-server        = utf8mb4_general_ci
```

Key settings include:
- `bind-address = 0.0.0.0` - Allows MariaDB to accept connections from all IP addresses
- `character-set-server = utf8mb4` - Uses UTF-8 character set (including emoji support)
- `query_cache_size = 16M` - Sets the query cache size

#### Database Creation Script (`create-db.sql`)
```sql
CREATE DATABASE IF NOT EXISTS wordpress;
CREATE USER IF NOT EXISTS 'ctasar'@'%' IDENTIFIED BY '12345';
GRANT ALL PRIVILEGES ON wordpress.* TO 'ctasar'@'%';
FLUSH PRIVILEGES;
ALTER USER 'root'@'localhost' IDENTIFIED BY 'root12345';
```

This SQL script:
1. Creates the WordPress database (if it doesn't already exist)
2. Creates the `ctasar` user and sets its password
3. Grants all privileges on the WordPress database to this user
4. Reloads privileges (flush privileges)
5. Sets a password for the root user

## Docker Compose Configuration
The `docker-compose.yml` file combines the three services (NGINX, WordPress, MariaDB) and defines their relationships:

```yaml
version: "3.8"

services:
  nginx:
    container_name: nginx
    build: ./requirements/nginx/.
    ports:
      - "443:443"
    depends_on:
      - wordpress
    volumes:
      - wordpress:/var/www/html
    networks:
      - inception
    env_file:
      - .env
    restart: always

  wordpress:
    container_name: wordpress
    build: ./requirements/wordpress/.
    depends_on:
      - mariadb
    volumes:
      - wordpress:/var/www/html
    env_file:
      - .env
    networks:
      - inception
    restart: always

  mariadb:
    container_name: mariadb
    build: ./requirements/mariadb/.
    volumes:
      - mariadb:/var/lib/mysql
    env_file:
      - .env
    networks:
      - inception
    restart: always

volumes:
  wordpress:
    name: wordpress
    driver: local
    driver_opts:
      device: ../data/wordpress
      o: bind
      type: none
  mariadb:
    name: mariadb
    driver: local
    driver_opts:
      device: ../data/mariadb
      o: bind
      type: none

networks:
  inception:
    name: inception
```

This configuration:
1. Defines three services (nginx, wordpress, mariadb)
2. Specifies dependencies between services (nginx → wordpress → mariadb)
3. Defines two persistent data volumes (wordpress, mariadb)
4. Creates a custom network (inception)
5. Sets the restart policy for services (always)

## Environment Variables (.env)
The project uses a `.env` file to retrieve various configuration values:

```properties
DOMAIN_NAME=ctasar.42.fr

WP_TITLE=inception

WP_ADMIN_USR=ctasar
WP_ADMIN_PWD=1234
WP_ADMIN_EMAIL=ctasar@42.fr

WP_USR=editor
WP_EMAIL=editor@gmail.com
WP_PWD=123
```

These variables:
1. Define the WordPress site name and domain
2. Specify the WordPress admin user credentials
3. Define a second WordPress user

## How It Works

1. When the `make` command is run, containers are created using Docker Compose
2. The MariaDB container starts first and prepares the database
3. The WordPress container starts next, connects to the database, and completes the WordPress setup
4. Finally, the NGINX container starts and provides external access to WordPress
5. All services communicate with each other via the "inception" Docker network
6. WordPress and MariaDB data are stored on persistent volumes

## Security Features

1. NGINX is accessible only via HTTPS (port 443)
2. TLS/SSL protocol version 1.3 is used
3. WordPress database configuration is securely set up
4. MariaDB root password is changed

## Technical Details

- Containerized services using Docker and Docker Compose
- Debian Bullseye base images
- NGINX web server and reverse proxy
- PHP 7.4 and PHP-FPM
- MariaDB database server
- WordPress CMS
- Secure connection with TLS/SSL certificate
- Persistent data storage using Docker volumes
- Inter-service communication via a custom Docker network
