FROM nginx:1.21.0-alpine

RUN apk update && \
    apk add --no-cache openssl && \
    mkdir -p /etc/nginx/certs && \
    openssl req -x509 -nodes -days 365 -subj "/C=CL/ST=CL/O=CL, Inc./CN=localhost" \
    -addext "subjectAltName=DNS:localhost.local" -newkey rsa:2048 -keyout /etc/nginx/certs/localhost_cert.key \
    -out /etc/nginx/certs/localhost_cert.crt

COPY default.conf /etc/nginx/conf.d/default.conf
