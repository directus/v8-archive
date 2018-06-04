# Directus API Guide

## Installation

Installing the Directus API is quite simple. However, with such a wide variety of possible server configurations we can't test/document them all. Please use the following as a general guide and let us know if there are other details that would be useful to include.

1. TODO

## System Requirements

* Apache, NGINX, or XXX
* MySQL X.X+
* PHP 5.X+

_Additionally, the following PHP extensions should be installed on your server:_

* PDO_MySQL
* Mod_Rewrite
* cURL
* FileInfo
* GD Library
* mcrypt

## Server-Specific Installs

### Apache

### Nginx

### Caddy

### Docker (?)

TODO

## Thumbnailer

Our thumbnailer dynamically returns images upon request based on parameters in the URL. Basic flow can be seen below, or view the full docs in the [Thumbnailer Repository](https://github.com/directus/directus-thumbnailer).

1. Request is made to thumbnailer with desired parameters (width, height, quality, crop), then:
2. Requests are routed through `.htaccess` (or equivilant), if file already exists it is simply returned, otherwise:
3. Parameters are checked against the config's whitelist, if not allowed then a [PLACEHOLDER](https://github.com/directus/directus-thumbnailer/blob/standalone/img-not-found.png) is returned, otherwise:
4. Image is generated on-the-fly based on parameters, saved to storage, and returned in the response.

### Configuration & Whitelisting

TODO

## Extensions

TODO

## Auth Providers

TODO
