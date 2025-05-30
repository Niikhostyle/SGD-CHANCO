#Imagen Base
FROM php:8.0.9-apache


#Instalar GIT, GnuPG, NodeJS y NPM
RUN apt-get update && \
    curl -sL https://deb.nodesource.com/setup_14.x | bash - && \
    apt-get install -y nodejs


#Instalar dependencias de Laravel (php extensions)
RUN apt-get update && apt-get install -y \
    unzip \
    vim \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    #&& docker-php-ext-install -j$(nproc) zip mysqli pdo_mysql \
    && docker-php-ext-install -j$(nproc) zip \
    #&& docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
        && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd
    #&& docker-php-ext-configure pdo_pgsql --with-pgsql \
    #&& docker-php-ext-install pdo_pgsql

#Instalar driver para pgsql
RUN apt-get install -y libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql

#Instalar paquete SOAP
RUN apt-get update -y \
  && apt-get install -y \
     libxml2-dev \
  && apt-get clean -y \
  && docker-php-ext-install soap

#Instalar paquete wkhtmltopdf
RUN apt-get update -y \
  && apt-get install -y \
     wkhtmltopdf

# Create working directory
RUN mkdir -p /var/www/sgd
ENV APACHE_DOCUMENT_ROOT /var/www/sgd/public
ENV APP_NAME "sgd"

# Install composer from image. You may change it to the latest
COPY --from=composer:2.2.18 /usr/bin/composer /usr/bin/composer

#RUN apt-get install curl nano && \
#    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/sgd

RUN sed -ri -e 's!/var/www/html!/var/www/sgd/public!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!/var/www/sgd/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini
RUN sed -i 's/upload_max_filesize = .*/upload_max_filesize = 100M/' /usr/local/etc/php/php.ini
RUN sed -i 's/post_max_size = .*/post_max_size = 100M/' /usr/local/etc/php/php.ini
RUN sed -i 's/memory_limit = .*/memory_limit = 512M/' /usr/local/etc/php/php.ini
RUN sed -i 's/memory_limit = .*/memory_limit = 512M/' /usr/local/etc/php/php.ini

RUN sed -i 's/expose_php = On/expose_php = Off/' /usr/local/etc/php/php.ini
RUN echo "ServerSignature Off\n" >> /etc/apache2/apache2.conf
RUN echo "ServerTokens Prod\n" >> /etc/apache2/apache2.conf
# Make laravel feel comfortable with mod-rewrite

#AGREGADO PARA CERTIFICADOS SSL
RUN sed -i 's/ssl-cert-snakeoil\.pem/certificado\.cert/g' /etc/apache2/sites-available/default-ssl.conf && sed -i 's/ssl-cert-snakeoil.key/certificado\.key/g' /etc/apache2/sites-available/default-ssl.conf
RUN a2ensite default-ssl
RUN apt-get update && apt-get upgrade -y
#FIN AGREGADO PARA CERTIFICADOS SSL

RUN a2enmod rewrite && a2enmod ssl && a2enmod socache_shmcb 
#&& service apache2 restart
#RUN service apache2 restart

EXPOSE 80
EXPOSE 443