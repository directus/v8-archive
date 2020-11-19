#!/usr/bin/env zsh

# Setup a temp folder we can clone the repos in
rm -rf ./_directus-v8-build;

# Clone and install everything we need for the API
cd api;

composer install;

# Clone, install and build everything for the app
cd ../app;

yarn;
yarn build;

cd ..;

# Move the app into the API
mv app/dist/ api/public/admin/;

mkdir _directus-v8-build;

# Copy the required files from the api into the directus master branch
cp -r ./api/{bin,composer.json,config,logs,migrations,public,src,vendor} _directus-v8-build/

echo "✨ All done!"
