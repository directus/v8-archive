---
pageClass: page-reference
---

# Projects

<two-up>

::: slot left
Projects are the individual tenants of the platform. Each project has its own database and data.
:::

<info-box title="Endpoints" slot="right">

```endpoints
   GET /:project
   GET /server/projects
  POST /server/projects
DELETE /server/projects/:project
```

</info-box>
</two-up>

---

## List Available Projects

<two-up>
<template slot="left">

Lists the available (public) projects in the API.

You can prevent projects from being returned in this endpoint by prefixing the config file of the project with `private.`, for example: `private.thumper.php` for the `thumper` project.

::: tip
This endpoint is always publicy accessible.
:::

#### Parameters

No URL parameters available.

#### Query

No query parameters available.

#### Returns

Array of project key strings.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /server/projects
```

</info-box>
<info-box title="Response">

```json
{
  "data": [
    "thumper",
    "thumper-staging",
    "monospace",
    "ranger"
  ],
  "public": true
}
```

</info-box>
</div>
</template>
</two-up>

---

## Retrieve Project Info

<two-up>
<template slot="left">

Gets information like name, locale, accent color about the given project.

::: tip
This endpoint is always publicy accessible. Logged in users get more information.
:::

### Parameters

<def-list>

!!! include params/project.md !!!

</def-list>

### Attributes

<def-list>

#### api.version <def-type alert>authenticated</def-type> <def-type>string</def-type>
Current version of the API in use.

#### api.requires2FA <def-type>boolean</def-type>
If the API requires 2FA for all its users.

#### api.database <def-type alert>authenticated</def-type> <def-type>string</def-type>
What database type is being used. 

#### api.project_logo <def-type>file object</def-type>
Nested file information for the project's logo.

#### api.project_color <def-type>string (hex)</def-type>
Project's accent color.

#### api.project_foreground <def-type>file object</def-type>
Nested file information for the project's public page's foreground.

#### api.project_background <def-type>file object</def-type>
Nested file information for the project's public page's background.

#### api.project_public_note <def-type>string</def-type>
Nested file information for the project's public note.

#### api.default_locale <def-type>string</def-type>
Locale string of the default language for the application.

#### api.telemetry <def-type>boolean</def-type>
Whether or not the API is allowed to send anonymous tracking information.

#### api.project_name <def-type>string</def-type>
The name of the project.

#### server.max_upload_size <def-type alert>authenticated</def-type> <def-type>string</def-type>
Maximum upload size in bytes that the server can accept.

#### server.general.php_version <def-type alert>authenticated</def-type> <def-type>string</def-type>
Current version of PHP that's being used in the server.

#### server.general.php_api <def-type alert>authenticated</def-type> <def-type>string</def-type>
How PHP is being run.

</def-list>

### Query

No query parameters available.

### Returns

Object of project and server information.

---

#### Public Pages
The `project_color`, `project_logo`, `project_foreground`, `project_background`, and `project_public_note` are used to build out the public pages of the admin app, like the login page.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /:project/
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "api": {
      "version": "8.3.1",
      "requires2FA": false,
      "database": "mysql",
      "project_name": "Thumper",
      "project_logo": {
        "full_url": "https://demo.directus.io/uploads/thumper/originals/19acff06-4969-5c75-9cd5-dc3f27506de2.svg",
        "url": "/uploads/thumper/originals/19acff06-4969-5c75-9cd5-dc3f27506de2.svg"
      },
      "project_color": "#4CAF50",
      "project_foreground": {
        "full_url": "https://demo.directus.io/uploads/thumper/originals/f28c49b0-2b4f-571e-bf62-593107cbf2ec.svg",
        "url": "/uploads/thumper/originals/f28c49b0-2b4f-571e-bf62-593107cbf2ec.svg"
      },
      "project_background": {
        "full_url": "https://demo.directus.io/uploads/thumper/originals/03a06753-6794-4b9a-803b-3e1cd15e0742.jpg",
        "url": "/uploads/thumper/originals/03a06753-6794-4b9a-803b-3e1cd15e0742.jpg"
      },
      "telemetry": true,
      "default_locale": "en-US",
      "project_public_note": "**Welcome to the Directus Public Demo!**\n\nYou can sign in with `admin@example.com` and `password`. Occasionally users break things, but don’t worry… the whole server resets each hour."
    },
    "server": {
      "max_upload_size": 20971520,
      "general": {
        "php_version": "7.2.24-0ubuntu0.18.04.1",
        "php_api": "apache2handler"
      }
    }
  }
}
```

</info-box>
</div>
</template>
</two-up>

---

## Create a Project

<two-up>
<template slot="left">

Create a new project. You are required to have an empty database and credentials to access it.

### Parameters

No URL parameters available.

### Attributes

<def-list>

#### project <def-type alert>required</def-type> <def-type>string</def-type>
Key for the project. This is used in the API URLs.

#### project_name <def-type>optional</def-type> <def-type>string</def-type>
Human friendly name for the project. Will be shown in the Directus admin app.

#### private <def-type>optional</def-type> <def-type>boolean</def-type>
Instantiate this project as a private project.

#### force <def-type>optional</def-type> <def-type>boolean</def-type>
Force the installation. This will overwrite whatever's there before. This will not alter any user created tables.

#### existing <def-type>optional</def-type> <def-type>boolean</def-type>
Overwrites existing Directus system collections. This will not alter any user created tables.

#### super_admin_token <def-type alert>required</def-type> <def-type>string</def-type>
The first time you create a project, the provided token will be saved and required for subsequent project installs. It can also be found and configured in `/config/__api.json` on your server.

#### db_host <def-type>optional</def-type> <def-type>string</def-type>
Host of the database. Defaults to `localhost`.

#### db_port <def-type>optional</def-type> <def-type>integer</def-type>
Port of the database. Defaults to `3306`.

#### db_name <def-type alert>required</def-type> <def-type>string</def-type>
Name of the database you're connecting to.

#### db_user <def-type alert>required</def-type> <def-type>string</def-type>
Database user that has permission to modify your database.

#### db_password <def-type>optional</def-type> <def-type>string</def-type>
Password for the database user.

#### user_email <def-type alert>required</def-type> <def-type>string</def-type>
Email address of the first admin user of the platform. New users can be added later using [the `/users` endpoint](/api/users).

#### user_password <def-type alert>required</def-type> <def-type>string</def-type>
Password for the first admin user of the platform.

#### user_token <def-type>optional</def-type> <def-type>string</def-type>
A static token for the user that can be used as access token for the API.

</def-list>

::: tip Other Configuration Objects
`cache`, `storage`, `auth`, `cors`, and `mail` configuration settings can be provided in this endpoint as well. See [the `_example.php` config file](https://github.com/directus/api/blob/84e1713296deaff288e0db0f54a119cf245aebcd/config/_example.php#L26-L150) for more information.
:::

### Query

No query parameters available.

### Returns

Empty payload with status 200 OK

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
  POST /server/projects
```

</info-box>

<info-box title="Request">

```json
{
  "project": "thumper",
  "super_admin_token": "very_secret_token",
  "db_name": "db",
  "db_user": "root",
  "db_password": "root",
  "user_email": "admin@example.com",
  "user_password": "password",
}
```

</info-box>
</div>
</template>
</two-up>

---

## Delete a Project

::: danger 
This will delete both the config file and empty the database. Use with extreme caution.
:::

<two-up>
<template slot="left">

Delete an existing project

### Parameters

<def-list>

!!! include params/project.md !!!

</def-list>

### Query

<def-list>

#### super_admin_token <def-type alert>required</def-type> <def-type>string</def-type>
The first time you create a project, the provided token will be saved and required for subsequent project installs. It can also be found and configured in `/config/__api.json` on your server.

</def-list>

### Returns

Returns an empty body with HTTP status 204

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
DELETE /server/projects/:project
```

</info-box>
</div>
</template>
</two-up>

---
