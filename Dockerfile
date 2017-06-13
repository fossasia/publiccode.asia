FROM php:7.0-apache

ENV GOPATH /srv/go
RUN mkdir /srv/go

RUN mkdir /usr/share/blog
RUN apt-get update && apt-get upgrade -y && \
    apt-get install -y git curl
RUN curl -O https://storage.googleapis.com/golang/go1.8.3.linux-amd64.tar.gz
RUN tar xvf go1.8.3.linux-amd64.tar.gz
RUN mv go /usr/local

RUN go get github.com/kardianos/govendor
RUN govendor get github.com/spf13/hugo

COPY site/ /usr/share/blog
RUN /usr/share/blog/build/build.sh
RUN rmdir /var/www/html/
RUN ln -s /usr/share/blog/public /var/www/html

