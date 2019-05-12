#!/bin/sh

# Wait for database server to be available
# https://github.com/ufoscout/docker-compose-wait
docker-compose-wait

# Source environment
if [ -f "./.env" ]; then
    . "./.env"
fi

# Install Composer packages
composer install

# Configure Directus API
# https://docs.directus.io/advanced/api/configuration.html#configure-with-script
bin/directus install:config -n ${DIRECTUS_API_DB_NAME:-directus} -u root -p ${DIRECTUS_API_DB_PASSWORD:-root} -h db -c "${DIRECTUS_API_ENABLE_CORS:-true}"
bin/directus install:database
bin/directus install:install -e "${DIRECTUS_ADMIN_EMAIL:-admin@directus.localhost}" -p "${DIRECTUS_ADMIN_PASSWORD:-admin}" -t "${DIRECTUS_ADMIN_PASSWORD:-admin}"

# Execute CMD
exec "$@"
