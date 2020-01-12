FROM python:2-slim-stretch as build

ENV LANG en_US.UTF-8
ENV BUILDDIR /data

RUN mkdir -p /usr/share/man/man1 \
  && apt-get update && apt-get install -yq python-sphinx plantuml make

COPY . /src

RUN mkdir /data \
  && cd /src \
  && make BUILDDIR=$BUILDDIR clean html

FROM nginx:alpine

RUN ln -sf /dev/stdout /var/log/nginx/access.log \
 && ln -sf /dev/stderr /var/log/nginx/error.log

COPY --from=build /data/html /usr/share/nginx/html/$DOCKER_TAG

STOPSIGNAL SIGQUIT

CMD ["nginx", "-g", "daemon off;"]
