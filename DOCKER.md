# Directus API : Docker documentation

## Directus API Docker image

This image is based on the Alpine Linux flavors of the official [PHP Docker images](https://hub.docker.com/_/php). It exposes the Directus API application via PHP-FPM on port 9000.

### Building the image

The image can be built using the regular `docker build` command.

Build's default configuration can be altered using the following build arguments :

- **`PHP_VERSION=7`** : Version de l'image [PHP](https://hub.docker.com/_/php)
- **`COMPOSER_VERSION=1`** : Version de l'image [Composer](https://hub.docker.com/_/composer)

## Setting up a development environment

Environment's default configuration can be altered by tweaking the variables in the [.env](.env) file.

### Docker Compose

[Docker Compose](https://docs.docker.com/compose/) can be used to setup a local development environment.

Running the command `docker-compose up` from the root of the Directus API directory will setup a working application instance :

- <http://api.directus.localhost:8080> : The Directus API application instance root URL

### Database

It can be managed using the [Adminer](https://www.adminer.org/) instance available at <http://adminer.api.directus.localhost:8080>.

Database data is persisted in the `var/db` directory.

### Router

[Traefik](https://traefik.io/) Router dashboard is available at <http://traefik.api.directus.localhost:8080>.
