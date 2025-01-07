FROM php:8.1 as base

# /usr/share/man/man1 needs to be created before installing openjdk-11-jre lest it will fail
# https://bugs.debian.org/cgi-bin/bugreport.cgi?bug=863199#23
RUN mkdir -p /usr/share/man/man1 \
    && apt-get update \
    && apt-get install --no-install-recommends -yq libicu-dev libicu72 zlib1g-dev ca-certificates-java gpg

RUN apt-get -yq install openjdk-17-jre-headless \
    && rm -rf /var/lib/apt/lists/* /usr/share/man/man1 \
    && docker-php-ext-install -j$(nproc) intl

FROM composer:2 AS build

COPY . /opt/phpdoc
WORKDIR /opt/phpdoc
RUN /usr/bin/composer install --prefer-dist -o --no-interaction --no-dev

FROM base as phpdoc_base

WORKDIR /data
VOLUME /data

ENV PHPDOC_ENV=prod
ENV PATH="/opt/phpdoc/bin:${PATH}"

COPY --from=build /opt/phpdoc /opt/phpdoc
RUN echo "memory_limit=-1" >> /usr/local/etc/php/conf.d/phpdoc.ini

ENTRYPOINT ["/opt/phpdoc/bin/phpdoc"]

COPY --from=composer /usr/bin/composer /usr/bin/composer

FROM phpdoc_base as prod

FROM prod as dev

RUN apt-get update \
    && apt-get install -yq git \
    && useradd -m -s /bin/bash phpdoc

FROM dev as dev-pcov

RUN pecl install pcov \
	&& docker-php-ext-enable pcov
