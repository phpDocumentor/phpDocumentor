FROM php:7.2

RUN yes | pecl install pcov \
    && echo "pcov.enabled=1" >> /usr/local/etc/php/conf.d/pcov.ini \
    && echo "pcov.directory=/opt/phpdoc" >> /usr/local/etc/php/conf.d/pcov.ini

RUN apt-get update \
    && apt-get install -yq graphviz curl git libicu-dev libicu63 zlib1g-dev gpg \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install -j$(nproc) intl zip \
    && docker-php-ext-enable pcov

WORKDIR /opt/phpdoc

CMD ["/bin/bash"]
