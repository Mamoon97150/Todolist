FROM php:8.0-fpm

RUN apt-get update && apt-get install -y --no-install-recommends zlib1g-dev g++ git libicu-dev zip libzip-dev zip \
    && docker-php-ext-install intl opcache pdo pdo_mysql \
    && pecl install apcu \
    && pecl install xdebug \
    && docker-php-ext-enable apcu xdebug\
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip \
    && apt-get clean \
 	&& rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/project

SHELL ["/bin/bash", "-o", "pipefail", "-c"]
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

SHELL ["/bin/bash", "-o", "pipefail", "-c"]
RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony