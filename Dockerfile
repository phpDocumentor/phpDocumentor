FROM composer:1.6.5 AS composer

FROM php:7.1

RUN apt-get update \
    && apt-get install -yq graphviz curl git libicu-dev libicu63 zlib1g-dev libxslt1-dev libxslt1.1 \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install -j$(nproc) intl zip xsl

WORKDIR /data
VOLUME /data

ENV PHPDOC_ENV=prod
ENV PATH="/opt/phpdoc/bin:${PATH}"

COPY . /opt/phpdoc
COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN cd /opt/phpdoc \
    && /usr/bin/composer install --prefer-dist -o --no-interaction --no-dev \
    && echo "memory_limit=-1" >> /usr/local/etc/php/conf.d/phpdoc.ini

ENTRYPOINT ["/opt/phpdoc/bin/phpdoc"]

