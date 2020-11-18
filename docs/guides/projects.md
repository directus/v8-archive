# Projects

> Each project represents a database. Projects can be used to organize different application schemas or environments.

## Multitenancy

A single instance of Directus can manage any number of project databases. Each project has its own database, storage adapter, and configuration file.

* [Learn more about Project Configuration](/advanced/api/configuration.html#config-file-options)
* [Learn more about Connecting to Projects](/api/reference.html#project-prefix)

## Creating a Project

1. Create a new database and database user
1. Run the Directus Installer by going to `/admin/#/install` in your Directus installation

::: warning Super Admin Tokens
When you create your first project, you'll be provided with a secure Super Admin Token that must be used to install any subsequent projects. This token is stored in `/config/__api.json`.
:::

:::warning Reserved Project Names
The following project names are reserved for system purposes and can not be used:

`server`, `interfaces`, `modules`, `layouts`, `types`
:::

## Deleting a Project

For security reasons, this can only be done manually via the following steps.

1. Delete the project's database
1. Delete the project's API config file
1. Delete any files in that project's storage adapter

## Private Projects

If you do not want to share the existence of a project with the outside world, you can make it private by prepending the configuration filename with `private.`. This will hide the project name from the project switcher, and prevents the project name from being returned by the `GET /server/projects` endpoint. Users can still login to this project by using the `project` query parameter mentioned above.

```
// Public Project Example
/config/my-project.php

// Private Project Example
/config/private.my-project.php
```

## Linking to a Project

The Directus login page provides a project chooser so that users can select from a listing of the public projects available. If a project is private, or you want to link to a specific project directly, you can use the following URL:

```
https://example.com/admin/#/login?project=my-project-key
```