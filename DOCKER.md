# Directus API : Docker documentation

## Directus API Docker image

This image is based on the Alpine Linux flavors of the official [PHP Docker images](https://hub.docker.com/_/php).

### Building the image

The image can be built using Docker's [build command](https://docs.docker.com/engine/reference/commandline/build/).

Build's default configuration can be altered using the following build arguments :

- **`PHP_VERSION=7`** : Version de l'image [PHP](https://hub.docker.com/_/php)
- **`COMPOSER_VERSION=1`** : Version de l'image [Composer](https://hub.docker.com/_/composer)

### Running containers

It is possible to run Directus API containers using the image built previously built using Docker's [run command](https://docs.docker.com/engine/reference/commandline/run/).

Containers expose the Directus API application via the PHP-FPM interface on port 9000.

## Setting up a development environment

Environment's default configuration can be altered by tweaking the variables in the [.env](.env) file.

### Docker Compose

[Docker Compose](https://docs.docker.com/compose/) can be used to setup a local development environment.

Running the command `docker-compose up` from the root of the Directus API directory will setup a working application instance :

- <http://api.directus.localhost:8080> : The Directus API application instance root URL

### Database

A dedicated database is created when `docker-compose up` is called for the first time.

Data can be queried and manipulated using the [Adminer](https://www.adminer.org/) instance available at <http://adminer.api.directus.localhost:8080>.

Database data is persisted in the `var/db` directory.

### Router

[Traefik](https://traefik.io/) Router dashboard is available at <http://traefik.api.directus.localhost:8080>.

## Resetting a development environment

The following commands will reset a development environment to its initial state by **erasing all data and custom configuration** :

```sh
docker-compose rm -f
docker rmi -f directus-api_api
sudo rm -rf var/db
rm -rf config/api.php
docker-compose up
```
