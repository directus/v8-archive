FROM directus/directus:v8-apache

# Install PHP extensions
RUN \
  php-ini-add "upload_max_filesize=16M" && \
  php-ini-add "post_max_size=22M"
