ARG PHP_VERSION=8.3

ARG UID=1000
ARG GID=1000

FROM php:${PHP_VERSION:-8.3}-fpm

RUN apt-get update \
    && apt-get install -y make libyaml-dev yamllint libpq-dev libicu-dev git libzip-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install \
    pdo_pgsql pgsql intl zip

RUN pecl install apcu yaml redis xdebug && docker-php-ext-enable yaml apcu redis intl xdebug

RUN echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

ENV PHP_VERSION=${PHP_VERSION}

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html

ARG USER_ID=1000
ARG GROUP_ID=1000
RUN chown -R ${USER_ID}:${GROUP_ID} /var/www
COPY --chown=${USER_ID}:${GROUP_ID} ./ /html
RUN groupmod -g ${GROUP_ID} www-data && \
    usermod -u ${USER_ID} www-data

USER "${USER_ID}:${GROUP_ID}"

USER www-data

EXPOSE 9000

CMD ["php-fpm"]