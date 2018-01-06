FROM phpstan/phpstan:latest
RUN apk add --update --no-cache icu-libs libxslt \
    && apk add --no-cache --virtual .build-deps icu-dev libxslt-dev \
    && docker-php-ext-install intl xsl \
    && apk del .build-deps \
    && rm -rf /var/cache/apk/*
