#!/usr/bin/env zsh

# Setup a temp folder we can clone the repos in
rm -rf ./tmp;
mkdir tmp;
cd tmp;

# Clone and install everything we need for the API
git clone git@github.com:directus/api.git -b master;
cd api;
php /usr/local/bin/composer.phar install -a --no-dev;

# Remove all nested .git stuff (from the vendor dependencies)
( find . -type d -name ".git" && find . -name ".gitignore" && find . -name ".gitmodules" ) | xargs rm -rf;

# Clone, install and build everything for the app
cd ../;
git clone git@github.com:directus/app.git -b master;
cd app;
yarn;
yarn build;
cd ..;

# Move the app into the API
mv app/dist/ api/public/admin/;

# Clone the directus/directus repo to get the static info files (license, readme, etc)
git clone git@github.com:directus/directus.git -b static-info static-info;

# Remove the .git folder from the static info (we want to recursively copy everything in it later)
rm -rf static-info/.git;

# Clone directus/directus itself on master (deploy target)
git clone git@github.com:directus/directus.git;

# Delete everything from directus/directus except the .git history
cd directus;
find . -path ./.git -prune -o -exec rm -rf {} \; 2> /dev/null;
cd ..;

# Move the static info files into the directus/directus master branch
cp -r static-info/ directus/;

# Copy the required files from the api into the directus master branch
cp -r ./api/{bin,composer.json,config,logs,migrations,public,src,vendor} ./directus/

cd directus;

# Get the version number from the user
echo "What version? (v8.x.x)"
read version

# Commit the changes
git add -A
git commit -m "$version"
git push origin master
cd ..
cd ..
rm -rf tmp
echo ""
echo "✨ All done!"
