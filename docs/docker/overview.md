# Directus with Docker

[[toc]]

## About

Directus provides several container images that will help you get started. Currently, all our container images are published in [Docker Hub](https://hub.docker.com/r/directus/directus) as `directus/directus`

## Configuring

Directus images can be configured using environment variables. These variables follow the same structure present in configuration files to make it easier for you to find which variable you need to change.

To see all available variables, please check the [Environment Variables](/docker/environment.md) page.

::: warning Required variables
You're **required** to provide both `DIRECTUS_AUTH_SECRETKEY` and `DIRECTUS_AUTH_PUBLICKEY` when using environment variables in order to protect your directus installation, and they shouldn't be equal. The container won't boot if any of them are missing or equals.
:::

### Disabling environment variables

To disable environment variable configuration, you should set `DIRECTUS_USE_ENV` to `0`. This will make the container load configs from disk as the normal installation does.

## Tags

We publish `directus/directus` image with several different tags in order to separate the image backend and to allow users to pin a specific version of directus if needed.

Our tags follows the pattern `directus/directus:{version}-{kind}` in which `{version}` is replaced with the version information, and `{kind}` is replaced with the image variant, for example `apache`.

### Kind

At this moment, we officially support only `apache` images, but we'll expand to more backends as users request it.

### Versions

For each release, we publish several different tags to allow version pinning of Directus. For example, whenever we make an `apache` release containing Directus `vX.Y.Z`, three tags will be pushed to Docker Hub.

- `directus/directus:vX-apache`
- `directus/directus:vX.Y-apache`
- `directus/directus:vX.Y.Z-apache`

These images will receive updates whenever a new release is made. You can update them by pulling the image again.

## Ports

A Directus container will currently listen on port `80`.

## SSL 

The current container images don't provide SSL support out of the box. In order to do this, you can use a reverse proxy in front of the Directus container, like [Traefik](https://docs.traefik.io/https/overview/), [Caddy](https://caddyserver.com/docs/automatic-https) or an external one like [Cloudflare](https://cloudflare.com).

## Volumes

The Directus image has some volumes that you might want to mount in order to keep your file uploads or configurations on disk.

### Configuration files

The configuration directory lives in `/var/directus/config`. You can mount it in case you're configuring your container through files instead of environment variables.

::: tip
You don't have to worry about this when you're using environment variables to configure Directus projects.
:::

### Uploads

The uploads directory lives in `/var/directus/public/uploads`. You can mount it to keep your uploads safe.

::: tip S3 Storage
To make your installation truly cloud-native, we highly recommend using a [S3-compatible server for storage](/docker/environment#storage).
:::

## Examples

You can find our official docker examples on [GitHub](https://github.com/directus/docker) under [`examples`](https://github.com/directus/docker/tree/master/examples) directory.

## Issues

You can find our official docker repository in [GitHub](https://github.com/directus/docker/) under [`directus/docker`](https://github.com/directus/docker/).
