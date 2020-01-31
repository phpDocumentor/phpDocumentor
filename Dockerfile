FROM php:7.1

ADD . /opt/phpdoc

RUN apt-get update \
    && apt-get install -yq graphviz curl git libicu-dev libicu63 zlib1g-dev libxslt1-dev libxslt1.1 \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install -j$(nproc) intl zip xsl \
    && cd /opt/phpdoc \
    && curl -O https://getcomposer.org/composer.phar \
    && php composer.phar install --prefer-dist -o --no-interaction --no-dev \
    && rm composer.phar

WORKDIR /data
VOLUME /data

ENTRYPOINT ["/opt/phpdoc/bin/phpdoc"]
