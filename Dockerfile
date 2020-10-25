FROM composer:2 AS composer

FROM php:7.2

# /usr/share/man/man1 needs to be created before installing openjdk-11-jre lest it will fail
# https://bugs.debian.org/cgi-bin/bugreport.cgi?bug=863199#23
RUN mkdir -p /usr/share/man/man1 \
    && apt-get update \
    && apt-get install -yq graphviz curl git libicu-dev libicu63 zlib1g-dev libzip-dev gpg openjdk-11-jre \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install -j$(nproc) intl zip

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

CMD ["help", "run"]
