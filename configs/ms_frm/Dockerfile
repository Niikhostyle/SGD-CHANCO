FROM php:8.0.9-cli-bullseye

#Instalar dependencias de Laravel (php extensions)
RUN apt-get update && apt-get install -y \
    unzip \
    vim \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    parallel \
    #&& docker-php-ext-install -j$(nproc) zip mysqli pdo_mysql \
    && docker-php-ext-install -j$(nproc) zip \
    #&& docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd
    #&& docker-php-ext-configure pdo_pgsql --with-pgsql \
    #&& docker-php-ext-install pdo_pgsql

# Install Ghostscript
RUN apt-get install -y ghostscript
    
# Install supervisor
RUN apt-get install -y supervisor

#Instalar driver para pgsql
RUN apt-get install -y libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql

#Instalar Java
RUN apt-get update && \
    apt-get install -y openjdk-11-jdk

ENV COMPOSER_HOME /composer
ENV PATH ./vendor/bin:/composer/vendor/bin:$PATH
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN curl -s https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin/ --filename=composer

ENV TZ=America/Santiago
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

COPY supervisord.conf /etc/supervisord.conf

CMD parallel ::: "php -S 0.0.0.0:3333 -t /src/public" "/usr/bin/supervisord -c /etc/supervisord.conf"