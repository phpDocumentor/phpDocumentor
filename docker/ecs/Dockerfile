FROM php:7.1

ARG VERSION="v4.6.0"

ENV COMPOSER_HOME /composer
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV PATH /composer/vendor/bin:$PATH

RUN apt-get update && apt-get install -y -q \
    curl \
    git \
    zlib1g-dev
#    php7 \
#    php7-json \
#    php7-phar \
#    php7-iconv \
#    php7-openssl \
#    php7-zlib \
#    php7-tokenizer \
#    php7-simplexml \
#    php7-xmlwriter \
#    php7-mbstring \
#    php7-ctype \
#    php7-xml \
RUN docker-php-ext-install -j$(nproc) zip
RUN curl -o /tmp/composer-setup.php https://getcomposer.org/installer \
  && curl -o /tmp/composer-setup.sig https://composer.github.io/installer.sig \
  && php -r "if (hash('SHA384', file_get_contents('/tmp/composer-setup.php')) !== trim(file_get_contents('/tmp/composer-setup.sig'))) { echo 'Invalid installer' . PHP_EOL; exit(1); }" \
  && php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer \
  && php -r "unlink('/tmp/composer-setup.php');" \
  && php -r "unlink('/tmp/composer-setup.sig');" \
  && composer global require symplify/easy-coding-standard:${VERSION} \
  && apt-get -yq remove curl git \
  && rm -rf /var/lib/apt/lists/*

VOLUME /app
WORKDIR /app

ENTRYPOINT ["ecs"]
