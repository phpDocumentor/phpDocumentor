FROM php:8.1 as base

# /usr/share/man/man1 needs to be created before installing openjdk-11-jre lest it will fail
# https://bugs.debian.org/cgi-bin/bugreport.cgi?bug=863199#23
RUN mkdir -p /usr/share/man/man1 \
    && apt-get update \
    && apt-get install --no-install-recommends -yq libicu-dev libicu72 zlib1g-dev ca-certificates-java gpg

RUN apt-get -yq install openjdk-17-jre-headless graphviz \
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
RUN echo "memory_limit=-1" >> /usr/local/etc/php/conf.d/phpdoc.ini && phpdoc cache:warm

ENTRYPOINT ["/opt/phpdoc/bin/phpdoc"]

FROM phpdoc_base as dev

RUN apt-get update \
    && apt-get install -yq git \
    && useradd -m -s /bin/bash phpdoc

COPY --from=composer /usr/bin/composer /usr/bin/composer

FROM phpdoc_base as prod

FROM prod as dev-pcov

RUN pecl install pcov \
	&& docker-php-ext-enable pcov
