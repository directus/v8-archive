# Build arguments
ARG PHP_VERSION=7
ARG COMPOSER_VERSION=1

# Base images
FROM composer:${COMPOSER_VERSION} as composer
FROM php:${PHP_VERSION}-fpm-alpine

# Install composer
COPY --from=composer /usr/bin/composer /usr/local/bin/composer

# Install Directus API dependencies
# - OS packages
# - PHP core extensions
# - PHP PECL extensions
RUN apk --update --no-cache add \
        curl \
        git \
        icu \
        imagemagick \
        libzip && \
    apk add --no-cache --virtual .build-deps \
        ${PHPIZE_DEPS} \
        curl-dev \
        gd-dev \
        icu-dev \
        imagemagick-dev \
        libpng-dev \
        libzip-dev \
        openssl-dev \
        zlib-dev && \
    docker-php-ext-install -j$(nproc) \
        exif \
        gd \
        intl \
        opcache \
        pdo_mysql && \
    pecl install \
        imagick \
        redis \
        zip && \
    docker-php-ext-enable \
        imagick \
        redis \
        zip && \
    apk del .build-deps && \
    rm -rf /tmp/* /usr/local/lib/php/doc/* /var/cache/apk/*

# Copy application sources
COPY . /var/www/html

# Install Composer packages
RUN composer install --prefer-dist --optimize-autoloader

# Make entrypoint script executable
RUN chmod 755 /var/www/html/docker-entrypoint.sh

# Install the docker-compose-wait utility. It is required for the initial Docker Compose run to succeed
ADD https://github.com/ufoscout/docker-compose-wait/releases/download/2.5.0/wait /usr/local/bin/docker-compose-wait
RUN chmod 755 /usr/local/bin/docker-compose-wait

# Set custom entrypoint
ENTRYPOINT ["/var/www/html/docker-entrypoint.sh"]
