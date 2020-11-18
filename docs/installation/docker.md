# Installing using Docker

If you're already familiar with Docker, you are probably looking for our official docker images over [Docker Hub](https://hub.docker.com/r/directus/directus). We have more detailed information about our docker images on [our docker documentation page](/docker/overview.md).

# Quick Start 

> Here's how you can get up and running fast with docker!

[[toc]]

## Step 1: Create a `docker-compose.yaml` file

This docker-compose defines our database and Directus service and links them.

```yaml
version: "3"

networks:
  directus:

services:
  mysql:
    image: mysql:5.7
    environment:
      MYSQL_DATABASE: "directus"
      MYSQL_USER: "directus"
      MYSQL_PASSWORD: "directus"
      MYSQL_ROOT_PASSWORD: "directus"
    ports:
      - "3306:3306"
    networks:
      - directus

  directus:
    image: directus/directus:v8-apache
    ports:
      - "8080:80"
    environment:
      DIRECTUS_APP_ENV: "production"
      DIRECTUS_AUTH_PUBLICKEY: "some random secret"
      DIRECTUS_AUTH_SECRETKEY: "another random secret"
      DIRECTUS_DATABASE_HOST: "mysql"
      DIRECTUS_DATABASE_PORT: "3306"
      DIRECTUS_DATABASE_NAME: "directus"
      DIRECTUS_DATABASE_USERNAME: "directus"
      DIRECTUS_DATABASE_PASSWORD: "directus"
    volumes:
      - ./data/config:/var/directus/config
      - ./data/uploads:/var/directus/public/uploads    
    networks:
      - directus
```

::: tip  
For a full overview of all available environment variables, go to [Docker Environment Overview](/docker/environment.html).
:::

::: tip
When using this in production, please make sure to set the database user and password, and the auth public and secret keys to something more secure.
:::

::: warning
If you already have another service running on port 8080 or 3306, change the values for `ports` in the `docker-compose.yaml` file above.
:::

## Step 2: Pull the latest images

```
docker-compose pull
```

## Step 3: Run the stack

```
docker-compose up -d
```

## Step 4: Initialize the database and an admin user

Wait until Docker is done booting up the stack. You can check this by running `docker ps`. When it's done, run the following command to finish up installation of Directus.

```
docker-compose run --rm directus install --email email@example.com --password d1r3ctu5
```

::: warning
Make sure to substitute `email@example.com` and `d1r3ctu5` with your preferred email and password.
:::

::: warning HTTPS
You are required to run Directus using HTTPS.
:::

## Step 5: Log in

Navigate to `http://localhost:8080/admin/#/login` and log in with the credentials in the previous step.
