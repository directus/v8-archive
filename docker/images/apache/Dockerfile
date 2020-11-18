#
# Final image
#
FROM php:7.3-apache

#
# Variables
#
ENV DIRECTUS_USE_ENV=1
ENV COMPOSER_ALLOW_SUPERUSER=1

#
# Dependencies & extensions
#
RUN \
  apt-get update && \
  apt-get install -y git zip libpng-dev libjpeg-dev libzip-dev libfreetype6-dev libxml2-dev libmagickwand-dev rsync && \
  rm -rf /var/lib/apt/lists/* && \
  docker-php-ext-configure zip --with-libzip && \
  docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ && \
  docker-php-ext-install gd zip exif pdo pdo_mysql xml fileinfo mysqli && \
  pecl install imagick && pecl install redis && \
  docker-php-ext-enable imagick && docker-php-ext-enable redis

#
# Filesystem & permissions
#
COPY ./rootfs/ /
RUN \
  find /var/directus/bin/ -type f -exec chmod +x "{}" \; && \
  cp /var/directus/bin/* /usr/local/bin/ && \
  rm -rf /var/directus/bin/ && \
  directus-setup initial

#
# Copy files
#

COPY --chown=www-data:www-data ./directus/ /var/directus/

#
# Final
#
WORKDIR /var/directus
RUN \
  directus-setup backup-volumes && \
  directus-setup install-dependencies && \
  composer require aws/aws-sdk-php && \
  composer require league/flysystem-aws-s3-v3 && \
  ( find . -type d -name ".git" && find . -name ".gitignore" && find . -name ".gitmodules" ) | xargs rm -rf

#
# Port
#
EXPOSE 80

#
# Volumes
#
VOLUME ["/var/directus/config", "/var/directus/public/uploads"]

#
# New entrypoint
#
ENTRYPOINT ["directus-entrypoint"]
CMD ["apache2-foreground"]
