FROM php:7.0-apache

ENV HUGO_VERSION 0.20.7
ENV HUGO_BINARY hugo_${HUGO_VERSION}_Linux-64bit.deb

RUN apt-get update && apt-get upgrade -y && \
    apt-get install -y git curl unzip python3

RUN curl -sS https://getcomposer.org/installer \
    | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer require phpmailer/phpmailer

ADD https://github.com/spf13/hugo/releases/download/v${HUGO_VERSION}/${HUGO_BINARY} /tmp/hugo.deb
RUN dpkg -i /tmp/hugo.deb \
	&& rm /tmp/hugo.deb

COPY site/ /usr/share/blog

COPY 000-default.conf /etc/apache2/sites-enabled/

CMD /usr/share/blog/build/build.sh /usr/share/blog/data/signatures/signatures.json && apache2-foreground
