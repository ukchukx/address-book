FROM php:7.3.6-fpm-alpine3.9

RUN docker-php-ext-install mbstring pdo pdo_mysql

# RUN apk --update add git autoconf g++ re2c make file libmcrypt-dev && \
#     rm -rf /var/cache/apk/* && \
#     docker-php-ext-install mbstring pdo pdo_mysql

# RUN cd /tmp/ && \
#     git clone https://github.com/igbinary/igbinary "php-igbinary" && \
#     cd php-igbinary && \
#     phpize && \
#     ./configure && \
#     make && \
#     make install && \
#     make clean && \
#     docker-php-ext-enable igbinary && \
#     pecl install --onlyreqdeps --nobuild redis && \
#     cd "$(pecl config-get temp_dir)/redis" && \
#     phpize && ./configure --enable-redis-igbinary && \
#     make && make install && make clean \
#     && docker-php-ext-enable redis && \
#     rm -rf /tmp/pear
RUN rm -rf /tmp/pear

WORKDIR /app
COPY . .

RUN mkdir bootstrap/cache; \
    chmod 777 -R bootstrap/cache; \
    php artisan cache:clear; \
    php artisan config:cache; \
    php artisan route:cache; \
    php artisan view:cache; \
    php artisan event-projector:cache-event-handlers

CMD php artisan address-book:init
