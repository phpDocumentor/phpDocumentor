FROM php:7

RUN apt-get update && apt-get install -y graphviz curl git
ADD . /opt/phpdoc
WORKDIR /data
VOLUME /data

ENTRYPOINT ["/opt/phpdoc/bin/phpdoc"]
