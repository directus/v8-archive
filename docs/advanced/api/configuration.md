# Configuring the API

> Each API project configuration is contained within a dedicated file in the `/config` directory. Additionally, this process adds a boilerplate system schema into the project's database.

::: tip
If you are using Apache, make sure `mod_rewrite` and `AllowOverride` are enabled. [Read more](/advanced/server-setup.md#apache)
:::

## Project Config Files

Each API instance can manage multiple projects. Each project has its own config, database, and file storage. Any extensions installed in the API will be available for all projects it manages.

Projects can be added with new config files, using this naming convention: `config/{my-project}.php`. Each project's config should point to a dedicated database and unique storage paths. Once configured, the API URL will be scoped to the project, eg: `https://api.example.com/my-project/collections`.

### Private / Public Projects

By default, the API will return all available projects in the API through the `/server/projects` endpoint. If you would like to prevent a project key from being returned in this endpoint, you can prepend the config file name with `private.` (for example `my-project.php` -> `private.my-project.php`).

Accessing a private project through the app can be done by linking directly to a scoped page (eg `/#/my-project/collections`) or adding the projects query parameter to the path (eg `/#/login?project=my-project`). This will force the app to try to read the project information from the given project. These methods can also be used to pre-select a project from the dropdown.

### Database Details

Next, update the `database` values with your own:

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

### Auth Keys

These keys can be anything, but we recommend a “strong” and unique value. They are unique identifiers that ensure your auth tokens are only able to be used within this project.

```
'auth' => [
  'secret_key' => '<secret-authentication-key>',
  'public_key' => '<public-authentication-key>',
  'social_providers' => [ ... ]
```
