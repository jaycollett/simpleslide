FROM php:8.2-apache

COPY ./src /var/www/html

RUN ln -s /images /var/www/html/images

# ENV Variables for our script and set some defaults
ENV pathtoimages=/images
ENV delayinsecs=30

EXPOSE 80

