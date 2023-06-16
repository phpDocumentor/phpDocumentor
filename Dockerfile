FROM composer:2 AS build

COPY . /opt/phpdoc
WORKDIR /opt/phpdoc
RUN /usr/bin/composer install --prefer-dist -o --no-interaction --no-dev

FROM php:8.1 as base

# /usr/share/man/man1 needs to be created before installing openjdk-11-jre lest it will fail
# https://bugs.debian.org/cgi-bin/bugreport.cgi?bug=863199#23
RUN mkdir -p /usr/share/man/man1 \
    && apt-get update \
    && apt-get install --no-install-recommends -yq graphviz libicu-dev libicu72 zlib1g-dev openjdk-17-jre-headless gpg \
    && rm -rf /var/lib/apt/lists/* /usr/share/man/man1 \
    && docker-php-ext-install -j$(nproc) intl

WORKDIR /data
VOLUME /data

ENV PHPDOC_ENV=prod
ENV PATH="/opt/phpdoc/bin:${PATH}"

COPY --from=build /opt/phpdoc /opt/phpdoc
RUN echo "memory_limit=-1" >> /usr/local/etc/php/conf.d/phpdoc.ini && phpdoc cache:warm

ENTRYPOINT ["/opt/phpdoc/bin/phpdoc"]

FROM base as dev

RUN apt-get update \
    && apt-get install -yq git

COPY --from=composer /usr/bin/composer /usr/bin/composer

FROM base as prod
