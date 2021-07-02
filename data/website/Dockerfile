from nginx:alpine

copy ./docs /usr/share/nginx/html/docs
copy ./demo /usr/share/nginx/html/demo
copy ./site /usr/share/nginx/html/site

COPY www.conf /etc/nginx/conf.d/default.conf
COPY demo.conf /etc/nginx/conf.d/demo.conf
COPY docs.conf /etc/nginx/conf.d/docs.conf
