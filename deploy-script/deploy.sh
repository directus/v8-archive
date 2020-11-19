#!/usr/bin/env zsh

# Setup a temp folder we can clone the repos in
rm -rf ./directus-build;

# Clone and install everything we need for the API
git clone git@github.com:directus/v8-archive.git -b master;
cd v8-archive/api;

php /usr/local/bin/composer.phar install -a --no-dev;

# Clone, install and build everything for the app
cd ../app;

yarn;
yarn build;

cd ..;

# # Move the app into the API
mv app/dist/ api/public/admin/;

cd ..;

mkdir directus-build;

# Copy the required files from the api into the directus master branch
cp -r ./v8-archive/api/{bin,composer.json,config,logs,migrations,public,src,vendor} directus-build/

rm -rf v8-archive;

echo "✨ All done!"
