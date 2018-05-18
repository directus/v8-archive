# Directus API 2.0

<p align="center">
<img src="https://camo.githubusercontent.com/ebf016c308b7472411bd951e5ee3c418a44c0755/68747470733a2f2f73332e616d617a6f6e6177732e636f6d2f662e636c2e6c792f6974656d732f33513238333030343348315931633146314b32442f64697265637475732d6c6f676f2d737461636b65642e706e67" alt="Directus Logo"/>
</p>

<p align="center">Open-Source Headless CMS API</p>
<p>&nbsp;</p>

**Important:** This API is currently under development for the next major [Directus](https://github.com/directus/directus) version. It is not ready to be used on production yet.

[![Join the chat at https://slack.getdirectus.com](https://img.shields.io/badge/chat-on%20slack-C7205A.svg)](https://slack.getdirectus.com)
[![GitHub issues](https://img.shields.io/github/issues/directus/api.svg)](https://github.com/directus/api/issues)
[![GitHub license](https://img.shields.io/badge/license-GPL-blue.svg)](https://raw.githubusercontent.com/directus/api/master/license.md)
[![StackShare](http://img.shields.io/badge/tech-stack-0690fa.svg?style=flat)](https://stackshare.io/ranger/directus)

## Requirements

* HTTP/Web Server
* MySQL 5.2+
* PHP 5.6+
    * PDO+MySql extension
    * cUrl extension
    * GD extension
    * FileInfo extension
    * Multibyte String extension 

## Installation

### HTTP Server

The root directory for public access should be `/path/to/directus/public`.

Read how to configure Directus on different HTTP Servers [here](https://github.com/directus/server-configs).

### Database

Install MySQL 5.2 or newer

### PHP

Install PHP 5.6 or newer and all the required dependencies

### API

Download the [latest release](https://github.com/directus/api/releases) or clone this repository.

#### From Source

You need to install composer if you are going to install from source.

```
$ git clone https://github.com/directus/api.git
$ cd api
$ composer install 
```

### Configuration

After composer install all the dependencies, you need to create a config file and a database.

### Manual

Create a config file by copying `config/api_sample.php` to `config/api.php` change the `database` configuration and everything should stay the same to work with default configuration.

Create the directus system tables and data by importing `src/schema.sql` to the database you just created and configured in your config file.

### Script

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

## Copyright, License, and Trademarks
* Directus Core codebase released under the [GPLv3](http://www.gnu.org/copyleft/gpl.html) license.
* Example Code, Design Previews, Demonstration Apps, Custom Extensions, Custom interfaces, and Documentation copyright 2017 [RANGER Studio LLC](http://rngr.org/).
* RANGER Studio owns all Directus trademarks, service marks, and graphic logos on behalf of our project's community. The names of all Directus projects are trademarks of [RANGER Studio LLC](http://rngr.org/).
