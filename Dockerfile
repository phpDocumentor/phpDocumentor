FROM php:7

ADD . /opt/phpdoc

RUN apt-get update \
    && apt-get install -yq graphviz curl git libicu-dev libicu57 zlib1g-dev \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install -j$(nproc) intl zip \
    && cd /opt/phpdoc \
    && curl -O https://getcomposer.org/composer.phar \
    && php composer.phar install --prefer-dist -o --no-interaction --no-dev \
    && rm composer.phar

WORKDIR /data
VOLUME /data

ENTRYPOINT ["/opt/phpdoc/bin/phpdoc"]
