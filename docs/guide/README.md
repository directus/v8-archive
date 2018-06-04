# Directus API Guide

## Installation

Installing the Directus API requires only a few steps. However, with such a wide variety of Operating Systems and HTTP/Web Servers, configuring Directus may be accomplished in different ways. This guide with walk you through how to install the API using common Web Servers and Operating Systems such as Ubuntu and Apache 2.

You are welcome to contribute details on how best to install the API in other development environments.

### System Requirements

* HTTP/Web Server
* MySQL 5.2+
* PHP 5.6+
    * PDO+MySql extension
    * cUrl extension
    * GD extension
    * FileInfo extension
    * Multibyte String extension 

For local development environments you can install WAMP, XAMP or MAMP using your OS package manager or by following the instructions on each product's installation page.

Another alternative is to use the [Directus Docker Image](https://github.com/directus/directus-docker) which contains everything you need to get up and running quickly.

### Get Directus

Find the [latest release](https://github.com/directus/api/releases) or [Download the latest build](https://github.com/directus/api/archive/build.zip) from GitHub.

#### From Source

If you want to install directus from source OR want to install the latest development version you need to clone the Directus API repository from `https://github.com/directus/api`.

**Requirements**
* [Git](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git) to fetch the source code from GitHub
* [Composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx) to install dependencies

```
# Get the source code
$ git clone https://github.com/directus/api.git
# Go to the api directory
$ cd api
# Install the dependencies using composer
$ composer install 
```

## Installing on Specific HTTP Web Servers

Directus has been tested on Apache 2, NGINX, and Caddy. While in theory it should work on any HTTP Server, each has a unique process for configuration. If you'd like to try installing on a different server type, you can start by looking at our current [server configurations here](https://github.com/directus/server-configs). Also, we'd love any pull-requests outlining steps for new server-types – just add them to [our list](https://github.com/directus/server-configs).

[Configuring Directus on Different HTTP Servers](https://github.com/directus/server-configs)

The root directory for Directus API should be `/path/to/directus/public`.

### Apache 2

[Apache 2 Configuration](https://github.com/directus/server-configs-apache).

### NGINX

[NGINX Configuration](https://github.com/directus/server-configs-nginx).

### Caddy

[Caddy Configuration](https://github.com/directus/server-configs-caddy).

## Database

Currently Directus only support MySQL and any drop-in alternatives, such as MariaDB or Percona.

The Directus API has been tested on MariaDB 10.x and MySQL 5.6+.

If you are not using a pre-packaged development environment (eg: MAMP), MySQL installation will differ depending on your Operating System. You can read more at the [MySQL Installation page](https://dev.mysql.com/doc/refman/8.0/en/installing.html).

## PHP

The API requires version 5.6 or newer. The PDO, mysql, cUrl, GD, FileInfo, and Multibyte String extensions are also required.

### PDO + MySQL
PHP uses PDO (PHP Data Objects) to connect and interact with a MySQL database using more secure _parameterized_ queries.

### cURL

cURL is used to fetch YouTube and Vimeo metadata (eg: title, description, and thumbnail) when adding new embeds.

### GD

GD is used by the [Thumbnailer](https://github.com/directus/directus-thumbnailer) to generate requested thumbnails of images.

If you want to generate thumbnails from SVG, PDF, PSD or TIF/TIFF you must also install and enable PHP's `ImageMagick` extension.

### FileInfo

This extension is used to get information and metadata (eg: charset and file-type) when uploading files. It also fetches additional information (eg: width, height, location, title, caption, and tags) when the file is an image based on any included [IPTC Metadata](https://iptc.org/standards/photo-metadata/).

### MultiByte String

The multibyte string functions are used by the `StringUtil` class to get a string's length or check if a string is contained within another.

## Configuration & Whitelisting

After you `composer install` all dependencies, you need to create a database and a config file with credentials used for connecting with the database.

### Create database

Connect to MySQL:

```
$ mysql -h <host> -u <user> -p
```

The command above will ask you for the user password:

```
$ mysql -h localhost -u root -p
Enter password: ****
```

After you successfully log into MySQL, run the `CREATE DATABASE` command:

```
mysql> CREATE DATABASE directus_test;
Query OK, 1 row affected (0.00 sec)
```

### Manual Installation

Now that we have a database, we need to create a config file by copying `config/api_sample.php` to `config/api.php` and setting the `database` credentials (the rest can stay the same for now).

```php
'database' => [
    'type' => 'mysql',
    'host' => 'localhost',
    'port' => 3306,
    'name' => 'directus_test',
    'username' => 'root',
    'password' => 'root',
    'engine' => 'InnoDB',
    'charset' => 'utf8mb4'
]
```

Finally we must import the Directus system tables and Data Primer into the database by importing the database dump in `/src/schema.sql`.

### Using Script

Create the config file:

```bash
$ bin/directus install:config -n <database-name> -u <mysql-user> -p <mysql-password>
```

Create the Directus system tables and data:

```bash
$ bin/directus install:database
```

Create the Directus default settings and user:

```bash
$ bin/directus install:install -e <admin-email> -p <admin-password> -t <project-title>
```

Test by requesting to view all users (the default `access_token` is `admin_token`):

```
GET http://localhost/_/users?access_token=admin_token
```

## Extensions

The API has been designed to be extensible, allowing you to add more third-party auth providers, endpoints, hashers, hooks, mail templates, database migrations, and seeders.

### Auth Providers

TODO

### Endpoints

TODO

### Hashers

TODO

### Hooks

Directus provides a list of events hooks that are triggered when an actions occurs. For example: after an item is updated.

There are two type of hooks, `actions` and `filters`.

- **Actions** execute a piece of code _without_ altering the data being passed through it
- **Filters** are the same as Actions but _can_ change the data passed through it

For example: an Action might send an email to user when an new article is created. While a Filter might set a UUID for a new article before it's inserted.

TODO

### Web Hooks

TODO

### Mail template

TODO

### Migrations

TODO
