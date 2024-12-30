FROM php:8.2-apache

# move our source code to the default path for apache
COPY ./src /var/www/html

# Link the webserver images folder to our volume of images so they can be served out
RUN ln -s /images /var/www/html/images

# ENV Variables for our script and set some defaults
ENV pathtoimages=/images
ENV delayinsecs=30

# expose the default 80 port for Apache, map this to whatever when running docker container
EXPOSE 80

