git subsplit init git@github.com:directus/api.git

# Collection
git subsplit publish --heads="master" --no-tags --debug src/core/Directus/Collection:git@github.com:directus/directus-collection.git

# Config
git subsplit publish --heads="master" --no-tags --debug src/core/Directus/Config:git@github.com:directus/directus-config.git

# Exception
git subsplit publish --heads="master" --no-tags --debug src/core/Directus/Exception:git@github.com:directus/directus-php-exception.git

# Permissions
git subsplit publish --heads="master" --no-tags --debug src/core/Directus/Permissions:git@github.com:directus/directus-permissions.git

# Database
git subsplit publish --heads="master" --no-tags --debug src/core/Directus/Database:git@github.com:directus/directus-database.git

# Filesystem
git subsplit publish --heads="master" --no-tags --debug src/core/Directus/Filesystem:git@github.com:directus/directus-filesystem.git

# Hash
git subsplit publish --heads="master" --no-tags --debug src/core/Directus/Hash:git@github.com:directus/directus-hash.git

# Hooks
git subsplit publish --heads="master" --no-tags --debug src/core/Directus/Hook:git@github.com:directus/directus-hook.git

# Utils
git subsplit publish --heads="master" --no-tags --debug src/core/Directus/Util:git@github.com:directus/directus-php-utils.git

# Helpers
git subsplit publish --heads="master" --no-tags --debug src/helpers:git@github.com:directus/directus-php-helpers.git

rm -rf .subsplit
