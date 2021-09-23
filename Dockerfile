FROM php:7.4-alpine
RUN docker-php-ext-install pdo pdo_mysql && docker-php-ext-enable pdo pdo_mysql