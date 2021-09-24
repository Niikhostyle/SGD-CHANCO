FROM php:8.0.9-cli-alpine3.14

RUN apk add --no-cache \
    yarn \
    autoconf \
    g++ \
    make \
    openssl-dev \
    bash \
    tzdata \
    oniguruma-dev \
    postgresql-dev \
    && docker-php-ext-install mbstring \
    && docker-php-ext-enable mbstring \
    && rm -rf /tmp/* \
    && docker-php-ext-install pdo pdo_pgsql \
    && docker-php-ext-enable pdo pdo_pgsql    

ENV COMPOSER_HOME /composer
ENV PATH ./vendor/bin:/composer/vendor/bin:$PATH
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN curl -s https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin/ --filename=composer

ENV TZ=America/Santiago
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

#WORKDIR /src/public
#RUN addgroup -S appgroup && adduser -S appuser -G appgroup
#RUN chown -R appuser:appgroup /src/public
#USER appuser

CMD php -S 0.0.0.0:3333 -t /src/public