FROM php:7.2

RUN apt-get update \
    && apt-get install -yq graphviz curl git libicu-dev libicu63 zlib1g-dev gpg \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install -j$(nproc) intl zip

CMD ["/bin/bash"]
