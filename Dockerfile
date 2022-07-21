FROM composer:2 AS build

COPY . /opt/phpdoc
WORKDIR /opt/phpdoc
RUN /usr/bin/composer install --prefer-dist -o --no-interaction --no-dev

FROM php:8.0

# /usr/share/man/man1 needs to be created before installing openjdk-11-jre lest it will fail
# https://bugs.debian.org/cgi-bin/bugreport.cgi?bug=863199#23
RUN mkdir -p /usr/share/man/man1 \
    && apt-get update \
    && apt-get install --no-install-recommends -yq graphviz libicu-dev libicu67 zlib1g-dev openjdk-11-jre-headless gpg \
    && rm -rf /var/lib/apt/lists/* /usr/share/man/man1 \
    && docker-php-ext-install -j$(nproc) intl

WORKDIR /data
VOLUME /data

ENV PHPDOC_ENV=prod
ENV PATH="/opt/phpdoc/bin:${PATH}"

COPY --from=build /opt/phpdoc /opt/phpdoc
RUN echo "memory_limit=-1" >> /usr/local/etc/php/conf.d/phpdoc.ini && phpdoc cache:warm

ENTRYPOINT ["/opt/phpdoc/bin/phpdoc"]

CMD ["help", "run"]
