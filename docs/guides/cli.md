# Command-Line Interface

> Directus CLI provides commands that allow you to perform various tasks such as installation, resetting a user's email, or upgrading the database to the most recent Directus schema.

## Commands List

| Name                                                 | Description                        |
| ---------------------------------------------------- | ---------------------------------- |
| [`install:config`](#configure-directus)              | Create a configuration file        |
| [`install:database`](#populate-the-database-schema)  | Create the default tables and data |
| [`install:install`](#install-initial-configurations) | Create initial configuration data  |
| [`db:upgrade`](#upgrade-directus-schema)             | Upgrade the Database Schema        |
| [`user:password`](#change-user-password)             | Change a user password             |
| [`log:prune`](#prune-old-log-files)                  | Remove old logs files              |

## Help

You can use the `help` command at any time to learn about available CLI actions:

```bash
# this will provide information about the current modules
php bin/directus help
```

To get more information on an specific command you can type "help" followed by the command:

```bash
# this provide information about the **install** module
php bin/directus help install
```

## Install Module

Includes commands to install and configure Directus.

### Configure Directus:

Creates the default `config/_.php` file.

:::warning
This command will overwrite any existing default configuration file at `config/_.php`.
:::

```bash
php bin/directus install:config -h <db_host> -n <db_name> -u <db_user> -p <db_pass> -e <directus_email> -s <db_unix_socket>
```

| Option         | Description
| -------------- | -----------------------------
| `t`            | Database type. (**Only `mysql` supported**)
| `h`            | Database host
| `P`            | Database port
| `n`            | Database name (it must already exist)
| `u`            | Database user's name
| `p`            | Database user's password
| `e`            | (Optional) The Directus email that will be used as sender in the mailing process
| `s`            | Database unix socket
| `a`            | Super Admin Token
| `c`            | Enable/Disable CORS
| `k`            | Unique Project's name (it must be required when creating new project in existing Directus setup)
| `timezone`     | API Server default timezone
| `f`            | Force file overwritten

#### Example: http://example.local

```bash
php bin/directus install:config -h localhost -n directus -u root -p pass -a super_admin_token -k my-project
```

#### Example: http://example.local/directus

```bash
php bin/directus install:config -h localhost -k directus -u root -p pass -n directus
```

### Populate the Database Schema:

Creates all of the Directus Core tables based on the configuration files: `/config/{project_name}.php`.

```bash
php bin/directus install:database -k <project_name>
```

### Install Initial Configurations:

Create the default admin user and the site's default settings.

```bash
php bin/directus install:install -e <admin_email> -p <admin_password> -t <site_name> -k <project_name>
```

| Option     | Description                                                  |
| ---------- | ------------------------------------------------------------ |
| `e`        | Admin email                                                  |
| `p`        | Admin password                                               |
| `T`        | Admin Static Auth Token                                      |
| `t`        | Project title                                                |
| `a`        | Project's Application URL                                    |
| `k`        | Unique Project's name                                        |
| `timezone` | Admin timezone                                               |
| `locale`   | Admin locale                                                 |
| `f`        | Recreate Directus core tables. Also remove all Directus data |

#### Example

```bash
php bin/directus install:install -e admin@directus.local -p password -t "Directus Example" -k directus
```

## User Module

Includes commands to manage Directus users

### Change User Password:

```bash
php bin/directus user:password -e <user_email> -p <new_password> -k <project_name>
```

- `user_email` - The user's email
- `new_password` - The user's new password
- `project_name` - The project name

#### Example

```bash
php bin/directus user:password -e admin@directus.local -p newpassword -k directus
```

## Database Module

Includes commands to manage Directus database schema

:::tip
This requires that Directus has a valid connection configured in `config/{project_name}.php`.
:::

:::warning
Always backup your database before running the database module to prevent data loss.
:::

### Upgrade Directus Schema

```
$ bin/directus db:upgrade -N <project-name>
```

:::tip
Don't forget to provide the project key when updating the database
:::

## Log Module

### Prune Old Log Files

```bash
php bin/directus log:prune <days>
```

`<days>` is optional. The default value is `30` days.

Removes all the logs that were last modified `<days>` ago. it uses [`filemtime`](http://php.net/manual/en/function.filemtime.php) function to determine the last modified time.

:::tip
You can setup a cronjob to clean old files at a set frequency
:::

## Maintenance Module

Enables/disables the maintenance mode. When maintenance mode is "on" the api returns all requests with `503 Service Unavailable` and a appropriate error message ([see Error Codes](../api/reference.md)).

`maintenance:status` tells you if maintenance mode is currently "on" or "off".

### Usage:

```bash
php bin/directus maintenance:on
php bin/directus maintenance:off
php bin/directus maintenance:status
```

:::tip
You can manually activate/deactivate the maintenance mode by creating/removing the file `logs/maintenance`
:::
