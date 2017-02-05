FROM php:7

EXPOSE 80

RUN mkdir -p /var/www/web

WORKDIR /var/www/web

RUN apt-get update \
    && apt-get install uuid-dev libicu-dev --yes \
    && rm -rf /var/lib/apt/lists/*

RUN pecl channel-update pecl.php.net \
    && pecl install uuid

RUN docker-php-ext-install intl opcache pdo_mysql \
    && docker-php-ext-enable uuid

# Install Composer.
RUN curl -o /tmp/composer-setup.php https://getcomposer.org/installer \
    && curl -o /tmp/composer-setup.sig https://composer.github.io/installer.sig \
    && php -r "if (hash('SHA384', file_get_contents('/tmp/composer-setup.php')) !== trim(file_get_contents('/tmp/composer-setup.sig'))) { unlink('/tmp/composer-setup.php'); echo 'Invalid installer' . PHP_EOL; exit(1); }" \
    && php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer \
    && rm -rf /tmp/composer-setup.php

# Copy the PHP Configuration.
COPY ./php.ini /usr/local/etc/php/

COPY ./ /var/www

RUN composer --working-dir=../ install

CMD /var/www/bin/console server:run 0.0.0.0:80
