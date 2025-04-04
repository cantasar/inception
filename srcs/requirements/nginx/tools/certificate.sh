#!/bin/bash

openssl req -x509 -nodes -days 365 -keyout /etc/ssl/private/ssl.key -out /etc/ssl/certs/ssl.crt -subj "/C=TR/ST=ISTANBUL/L=SARIYER/O=42/CN=ctasar.42.fr";

nginx -g "daemon off;"