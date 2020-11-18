FROM directus/directus:v8-apache

# Install PHP extensions
RUN \
  pecl install redis && \
  docker-php-ext-enable redis

# Install composer packages
RUN \
  composer require league/flysystem-aws-s3-v3
