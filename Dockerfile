FROM php:7.3.6-fpm-alpine3.9

RUN apk --update add git autoconf g++ re2c make file libmcrypt-dev && \
    rm -rf /var/cache/apk/* && docker-php-ext-install mbstring pdo pdo_mysql

RUN cd /tmp/ && git clone https://github.com/igbinary/igbinary "php-igbinary" && \
    cd php-igbinary && \
    phpize && \
    ./configure && \
    make && \
    make install && \
    make clean && \
    docker-php-ext-enable igbinary && \
    pecl install --onlyreqdeps --nobuild redis && \
    cd "$(pecl config-get temp_dir)/redis" && \
    phpize && ./configure --enable-redis-igbinary && \
    make && make install && make clean \
    && docker-php-ext-enable redis \
    && rm -rf /tmp/pear

WORKDIR /app
COPY . /app

ENV REDIS_PORT=6378

RUN mkdir bootstrap/cache; chmod 777 -R bootstrap/cache; php artisan cache:clear

CMD php artisan init
