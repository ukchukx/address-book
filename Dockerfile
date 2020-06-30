FROM php:7.3.6-fpm-alpine3.9

RUN docker-php-ext-install mbstring pdo pdo_mysql; rm -rf /tmp/pear

WORKDIR /app
COPY . .

RUN mkdir bootstrap/cache; \
    chmod 777 -R bootstrap/cache; \
    php artisan cache:clear; \
    php artisan config:cache; \
    php artisan route:cache; \
    php artisan view:cache; \
    php artisan event-sourcing:cache-event-handlers

CMD php artisan app:init
