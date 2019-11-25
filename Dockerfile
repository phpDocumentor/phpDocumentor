FROM php:7.3

RUN apt-get update \
    && apt-get install -yq graphviz curl git libicu-dev libicu63 zlib1g-dev libzip-dev \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install -j$(nproc) intl zip

WORKDIR /data
VOLUME /data

COPY . /opt/phpdoc

ENV PHPDOC_ENV=prod
ENV PATH="/opt/phpdoc/bin:${PATH}"
RUN cd /opt/phpdoc \
    && curl -O https://getcomposer.org/download/1.6.5/composer.phar \
    && chmod +x bin/phpdoc \
    && php composer.phar install --prefer-dist -o --no-interaction --no-dev \
    && rm composer.phar

RUN echo "memory_limit=-1" >> /usr/local/etc/php/conf.d/phpdoc.ini

ENTRYPOINT ["/opt/phpdoc/bin/phpdoc"]
