ARG NGINX_VERSION_MAJOR
FROM florentpassinge/alita-ngnix:latest

COPY nginx.conf /etc/nginx/nginx.conf
ADD sites-enabled /etc/nginx/sites-enabled
RUN rm -rf /etc/nginx/conf.d/default.conf