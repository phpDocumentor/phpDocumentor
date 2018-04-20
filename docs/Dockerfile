FROM python:2-slim-stretch

ENV LANG en_US.UTF-8
ENV BUILDDIR /var/www
RUN mkdir -p /usr/share/man/man1 \
  && apt-get update && apt-get install -yq python-sphinx plantuml make \
  && apt-get install --no-install-recommends -yq nginx \
  && rm -rf /var/lib/apt/lists/*

ADD . /src

RUN  cd /src \
  && make BUILDDIR=$BUILDDIR clean html \
  && apt-get remove -yq python-sphinx plantuml make \
    # forward request and error logs to docker log collector
    && ln -sf /dev/stdout /var/log/nginx/access.log \
    && ln -sf /dev/stderr /var/log/nginx/error.log

EXPOSE 80

STOPSIGNAL SIGTERM

CMD ["nginx", "-g", "daemon off;"]
