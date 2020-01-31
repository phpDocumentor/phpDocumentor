FROM nginx:alpine

ARG VERSION="latest"

RUN ln -sf /dev/stdout /var/log/nginx/access.log \
 && ln -sf /dev/stderr /var/log/nginx/error.log

COPY ./ /usr/share/nginx/html/$VERSION
COPY nginx.conf /etc/nginx/conf.d/default.conf
COPY ./redirectToLatest.html /usr/share/nginx/html/index.html
COPY ./redirectToLatest.html /usr/share/nginx/html/404.html

RUN rm /usr/share/nginx/html/$VERSION/nginx.conf \
    /usr/share/nginx/html/$VERSION/redirectToLatest.html \
    /usr/share/nginx/html/$VERSION/Dockerfile

STOPSIGNAL SIGQUIT
