# npm install
# npm install -g karma-cli
composer install
docker run --name mysql -p 127.0.0.1:8806:3306 \
           -e MYSQL_ROOT_PASSWORD=password -e MYSQL_DATABASE=directus -e MYSQL_USER=directus \
           -e MYSQL_PASSWORD=directus -d mysql:$MYSQL_VERSION
bin/directus install:config -h "127.0.0.1" -P 8806 -n "$DB_NAME" -u "$DIRECTUS_DB_USER" -p "$DIRECTUS_DB_PASSWORD" -d "$DIRECTUS_PATH" -e "$DIRECTUS_ADMIN_EMAIL"
bin/directus install:database
bin/directus install:install -e "$DIRECTUS_ADMIN_EMAIL" -p "$DIRECTUS_ADMIN_PASSWORD" -t "$DIRECTUS_SITE_NAME"
