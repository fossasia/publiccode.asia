FROM php:7.0-apache

ENV GOPATH /srv/go
RUN mkdir /srv/go

RUN mkdir /usr/share/blog
RUN apt-get update && apt-get upgrade -y && \
    apt-get install -y golang-go git
RUN go get github.com/kardianos/govendor
RUN govendor get github.com/spf13/hugo

COPY site/ /usr/share/blog
RUN /usr/share/blog/build/build.sh
RUN rmdir /var/www/html/
RUN ln -s /usr/share/blog/public /var/www/html

