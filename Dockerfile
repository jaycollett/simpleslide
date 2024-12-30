FROM php:8.2-apache

# move our source code to the default path for apache
COPY ./src /var/www/html

# ENV Variables for our script and set some defaults
ENV delayinsecs=30

# expose the default 80 port for Apache, map this to whatever when running docker container
EXPOSE 80

