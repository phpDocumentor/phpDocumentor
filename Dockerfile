FROM php:7

RUN apt-get update \
    && apt-get install -yq graphviz curl git libicu-dev libicu57 zlib1g-dev libxslt1-dev \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install -j$(nproc) intl zip xsl

WORKDIR /data
VOLUME /data

ADD . /opt/phpdoc

ENV APP_ENV=prod
ENV PATH="/opt/phpdoc/bin:${PATH}"
RUN cd /opt/phpdoc \
    && curl -O https://getcomposer.org/download/1.6.5/composer.phar \
    && chmod +x bin/phpdoc \
    && php composer.phar install --prefer-dist -o --no-interaction --no-dev \
    && rm composer.phar

ENTRYPOINT ["/opt/phpdoc/bin/phpdoc"]
