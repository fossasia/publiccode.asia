FROM php:7.0-apache

RUN mkdir /usr/share/blog
RUN apt-get update && apt-get upgrade -y && \
    apt-get install -y hugo

COPY site/ /usr/share/blog
RUN /usr/share/blog/build/build.sh
RUN rmdir /var/www/html/
RUN ln -s /usr/share/blog/public /var/www/html

