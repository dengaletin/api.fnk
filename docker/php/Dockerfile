FROM php:7.1-cli

RUN apt-get update && apt-get install -y \
    libpng-dev && \
    php -i && \
    docker-php-ext-install --help && \
    docker-php-ext-install -j$(nproc) pdo pdo_mysql gd zip

RUN yes | pecl install xdebug

RUN echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_host=host.docker.internal" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_port=9999" >> /usr/local/etc/php/conf.d/xdebug.ini

ENV APP_DIR /app

WORKDIR $APP_DIR
#VOLUME $APP_DIR

EXPOSE 80

CMD ["php", "-S", "0.0.0.0:80", "-t", "/app/web"]