#!/bin/sh

# Fail early
set -e

rm -rf .directus-build
git clone https://github.com/directus/directus.git .directus-build

cd .directus-build || exit
npm install
gulp build
gulp deploy
cd - || exit

rm -rf .directus-build
