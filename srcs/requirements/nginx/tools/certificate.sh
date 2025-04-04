#!/bin/bash

openssl req -x509 -nodes -days 365 -keyout /etc/ssl/private/nginx.key -out /etc/ssl/certs/nginx.crt -subj "/C=TR/ST=ISTANBUL/L=SARIYER/O=42/CN=ctasar.42.fr";

nginx -g "daemon off;"