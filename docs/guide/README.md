# Directus API Guide

## Installation

Installing the Directus API requires few steps. However, with such a wide variety of Operative Systems and HTTP/Web Servers, configuring Directus can be done in different ways. This guide with walk you through how to install it using common-known Web Servers and Operative system such as Ubuntu and Apache 2.

You are welcome to contribute details on how to install it in other development environments.

## System Requirements

* HTTP/Web Server
* MySQL 5.2+
* PHP 5.6+
    * PDO+MySql extension
    * cUrl extension
    * GD extension
    * FileInfo extension
    * Multibyte String extension 

You can install development environment stack such as WAMP, XAMP or MAMP all these are installed together or install them separately using your OS package manager or any other way you can find in each product installation page.

An alternative solution is to use the [Directus Docker Image](https://github.com/directus/directus-docker)

### Get Directus

Find the [latest release](https://github.com/directus/api/releases) or [Download the latest build](https://github.com/directus/api/archive/build.zip) from GitHub.

#### From Source

If you want to install directus from source OR want to install the latest development version you need to clone the directus api repo from `https://github.com/directus/api`.

**Requirements**
* [Composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx) to install all the dependencies.
* [Git](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git) to fetch the source code from GitHub.

```
# Get the source code
$ git clone https://github.com/directus/api.git
# Go to the api directory
$ cd api
# Install the dependencies using composer
$ composer install 
```

## HTTP Web Server-Specific Installs

Directus has been tested on Apache 2, NGINX and Caddy.

While in theory can work in any HTTP Server, each have their own way of configuration. Taking at look at our [server configurations](https://github.com/directus/server-configs) will help you create a one for another http server. If you do, please add it to [our list](https://github.com/directus/server-configs).

The root directory for Directus API should be `/path/to/directus/public`.

Read more on how to configure Directus on different HTTP Servers [here](https://github.com/directus/server-configs).

### Apache 2

Read how to configure Directus on Apache 2 HTTP Server [here](https://github.com/directus/server-configs-apache).

### Nginx

Read how to configure Directus on NGINX HTTP Server [here](https://github.com/directus/server-configs-nginx).

### Caddy

Read how to configure Directus on Caddy HTTP Server [here](https://github.com/directus/server-configs-caddy).

## Database

Currently Directus only support MySQL and any drop-in alternatives such as MariaDB and Percona.

Directus has been tested on MariaDB 10.x and MySQL 5.6+.

If you are not using any pre-packaged development environment you can install MySQL in different ways and it will depend on the server operative system.

You can read more at the [MySQL Installation page](https://dev.mysql.com/doc/refman/8.0/en/installing.html).

## PHP

The API requires version 5.6 or newer. The PDO, mysql, cUrl, GD, FileInfo and and Multibyte String extension are also required.

### PDO + MySQL
PHP uses PDO to connect and interact with a mysql database.

### curl

curl is used to fetch Youtube and Vimeo metadata such as video title, description and thumbnail when the user uploads a new file using a youtube or vimeo link.

### GD

GD is used by the thumbnailer to generate thumbnails from images files only.

If you want to generate thumbnails from SVG, PDF, PSD or TIF/TIFF you must install and enable `ImageMagick` extension.

### FileInfo

This extension is used to get the file being upload information and meta information such as charset and type of file, and if it's an image it will try to fetch the width and height, location, title, caption and tags. This are based on [IPTC Photo Metadata](https://iptc.org/standards/photo-metadata/).

### Multibyte String

The multibyte string functions are used by the `StringUtil` class to get a string length or check if a string is being contained into another.

## Configuration & Whitelisting

After composer install all the dependencies, you need to create a database and a config file to connect to the database.

### Create database

Connect to MySQL:

```
$ mysql -h <host> -u <user> -p
```

The command above will ask you for the user password.

```
$ mysql -h localhost -u root -p
Enter password: ****
```

After you have successfully log into MySQL run the `CREATE DATABASE` command:

```
mysql> CREATE DATABASE directus_test;
Query OK, 1 row affected (0.00 sec)
```

### Manual

Now that we have the database, we need to create a config file by copying `config/api_sample.php` to `config/api.php` and set the `database` configuration and the rest can stay the same for now.

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

Let's import the directus system tables and data into the new database you just created by importing the database dump in `src/schema.sql`.

### Using Script

Create the config file:

```bash
$ bin/directus install:config -n <database-name> -u <mysql-user> -p <mysql-password>
```

Create the directus system tables and data

```bash
$ bin/directus install:database
```

Create the directus default settings and user

```bash
$ bin/directus install:install -e <admin-email> -p <admin-password> -t <project-title>
```

The default `access_token` is `admin_token`.

Make you first request to see all the users:

```
GET http://localhost/_/users?access_token=admin_token
```

## Extensions

The API has been designed to be extensible, allowing you to add more third party auth providers, endpoints, hashers, hooks, mail templates, and database migrations and seeders.

### Auth Providers

TODO

### Endpoints

TODO

### Hashers

TODO

### Hooks

Directus provide a list of events that are called hooks that will be trigger when certain actions occours, such as before or after a item is inserted.

There is two type of hooks, `actions` and `filters`.

- Actions executed a piece of code without altering the data being passed.

- Filters do the same thing as actions with the only different that it can change the data that goes through all the filters hooks.

In a real world example an Action can be sending an email to user when an new article is created, and a Filters will be set a uuid for a new article before is inserted.

TODO

### Web Hooks

TODO

### Mail template

TODO

### Migrations

TODO