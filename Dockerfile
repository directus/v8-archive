#
# Builder
#
FROM composer as builder

# Copy source to builder
WORKDIR /directus/
COPY . /directus/

# Install dependencies
RUN composer install --ignore-platform-reqs

#
# Project
#
FROM alpine:3.9

# Ensure www-data user exists
RUN set -x \
	&& addgroup -g 82 -S www-data \
	&& adduser -u 82 -D -S -G www-data www-data
# 82 is the standard uid/gid for "www-data" in Alpine
# https://git.alpinelinux.org/aports/tree/main/apache2/apache2.pre-install?h=3.9-stable
# https://git.alpinelinux.org/aports/tree/main/lighttpd/lighttpd.pre-install?h=3.9-stable
# https://git.alpinelinux.org/aports/tree/main/nginx/nginx.pre-install?h=3.9-stable

# Allow running as an arbitrary user (https://github.com/docker-library/php/issues/743)
RUN set -eux; \
	[ ! -d /var/www/html ]; \
	mkdir -p /var/www/html; \
	chown www-data:www-data /var/www/html; \
    chmod 777 /var/www/html

# System dependencies
RUN apk add --no-cache \
    supervisor \
    socat \
    nginx \
    curl \
    zip \
    unzip \
    imagemagick

# PHP dependencies
RUN apk add --no-cache \
    php7 \
    php7-fpm \
    php7-curl \
    php7-dom \
    php7-exif \
    php7-fileinfo \
    php7-gd \
    php7-iconv \
    php7-intl \
    php7-json \
    php7-mbstring \
    php7-opcache \
    php7-openssl \
    php7-pdo \
    php7-pdo_mysql \
    php7-redis \
    php7-session \
    php7-xml \
    php7-xmlreader \
    php7-zip \
    php7-zlib \
    php7-imagick

# Configuration files
COPY ./docker/rootfs /
RUN touch /var/run/php-fpm7.sock && \
    chown www-data:www-data /var/run/php-fpm7.sock

# Scripts
RUN chmod +x /scripts/init
RUN chmod +x /scripts/entrypoint

# Copy Directus API files
COPY --from=builder --chown=www-data /directus/ /var/www/html/
COPY --chown=www-data ./docker/api.php /var/www/html/config/api.php

# Entrypoint
ENTRYPOINT ["/scripts/entrypoint"]
