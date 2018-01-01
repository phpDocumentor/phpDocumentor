FROM php:7

RUN apt-get update \
    && apt-get install -yq graphviz curl git libicu-dev libicu57 zlib1g-dev \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install -j$(nproc) intl zip

WORKDIR /data
VOLUME /data

ADD . /opt/phpdoc

RUN cd /opt/phpdoc \
    && curl -O https://getcomposer.org/composer.phar \
    && php composer.phar install --prefer-dist -o --no-interaction --no-dev \
    && rm composer.phar

ENTRYPOINT ["/opt/phpdoc/bin/phpdoc"]
