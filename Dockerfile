FROM php:7-fpm

RUN apt-get update \
    && apt-get install libicu-dev --yes \
    && docker-php-ext-install -j$(nproc) intl pdo pdo_mysql

RUN pecl channel-update pecl.php.net \
    && pecl install apcu

RUN version=$(php -r "echo PHP_MAJOR_VERSION.PHP_MINOR_VERSION;") \
    && curl -A "Docker" -o /tmp/blackfire-probe.tar.gz -D - -L -s https://blackfire.io/api/v1/releases/probe/php/linux/amd64/$version \
    && tar zxpf /tmp/blackfire-probe.tar.gz -C /tmp \
    && mv /tmp/blackfire-*.so $(php -r "echo ini_get('extension_dir');")/blackfire.so
