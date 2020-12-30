FROM php:8-fpm
RUN apt-get update && apt-get install -y libpq-dev \
librabbitmq-dev parallel git
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql
RUN docker-php-ext-install pdo pdo_pgsql

ADD extensions/rabbitmq.sh /root/install-rabbitmq.sh
RUN sh /root/install-rabbitmq.sh

RUN docker-php-ext-enable amqp
WORKDIR /usr/share/nginx/html/