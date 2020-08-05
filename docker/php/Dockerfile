FROM php:7.4-fpm
RUN apt-get update && apt-get install -y libpq-dev \
librabbitmq-dev parallel
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql
RUN docker-php-ext-install pdo pdo_pgsql
RUN pecl install amqp
RUN docker-php-ext-enable amqp
WORKDIR /usr/share/nginx/html/
