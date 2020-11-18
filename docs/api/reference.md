---
title: API Reference
pageClass: page-reference
---

# API Reference

<two-up>

::: slot left

Directus offers both a [RESTful](https://restfulapi.net/) and [GraphQL](https://graphql.org/) API to manage the data in the database. The API has predictable resource-oriented URLs, relies on standard HTTP status codes, and uses JSON for input and output.

The input/output of the API differs greatly for individual installs, as most of the endpoints will return data that's based on your specific schema.
:::

::: slot right
**New to Directus?**

See our [Getting Started](/getting-started/introduction.html) guide if this is your first time working with Directus.
:::

</two-up>

---

## Projects

<two-up>

::: slot left

Directus supports multi-tenancy out of the box. In order to do this, most endpoints will be prefixed with a project key based on the configuration filename.

When installing Directus for the first time, you'll be asked to provide a project key for your project. This is the project key that Directus expects in endpoints that require the `project` attribute.

:::

<info-box title="Example" slot="right">

```
/:project/items/:collection
/:project/activity
/system/info
```

</info-box>
</two-up>

---

## Authentication

<two-up>

<template slot="left">

By default, all data in the system is off limits for unauthenticated users. To gain access to protected data, you must include an access token with every request.

You pass the token in either the Authorization header, or a query parameter:

```
Authorization: bearer t0k3n
?access_token=t0k3n
```

To learn more, checkout [the authentication reference](/api/authentication).

</template>

<info-box slot="right" title="Endpoints">

```endpoints
  POST /:project/auth/authenticate
  POST /:project/auth/refresh
  POST /:project/auth/password/request
  POST /:project/auth/password/reset
   GET /:project/auth/sso
   GET /:project/auth/sso/:provider
   GET /:project/auth/sso/:provider/callback
```

</info-box>
</two-up>

---

## Errors

<two-up>

::: slot left
Directus relies on standard HTTP status code to indicate the status of a request. Next to that, the API uses numeric codes to avoid the need for translated error messages based on locale. The error property is only present when an error has occurred.

[Click here for the full list of error codes.](/api/errors)
:::

</two-up>

---

## Endpoints

### Items

<two-up>

::: slot left
Items are individual pieces of data in your database. They can be anything, from articles, to IoT status checks.

[Open items reference](/api/items.html)
:::

<info-box title="Endpoints" slot="right">

```endpoints
   GET /:project/items/:collection
   GET /:project/items/:collection/:id
  POST /:project/items/:collection
 PATCH /:project/items/:collection/:id
DELETE /:project/items/:collection/:id
   GET /:project/items/:collection/:id/revisions
   GET /:project/items/:collection/:id/revisions/:offset
 PATCH /:project/items/:collection/:id/revert/:revision
```

</info-box>
</two-up>

### Files

<two-up>

::: slot left
Files can be saved in any given location. Directus has a powerful assets endpoint that can be used to generate thumbnails for images on the fly.

[Open files reference](/api/files.html)
:::

<info-box title="Endpoints" slot="right">

```endpoints
   GET /:project/files
   GET /:project/files/:id
  POST /:project/files
 PATCH /:project/files/:id
DELETE /:project/files/:id
   GET /:project/files/:id/revisions
   GET /:project/files/:id/revisions/:offset
 PATCH /:project/files/:id/revert/:revision
```

</info-box>
</two-up>

### Activity

<two-up>

::: slot left
All events that happen within Directus are tracked and stored in the activities collection. This gives you full accountability over everything that happens.

[Open activity reference](/api/activity.html)
:::

<info-box title="Endpoints" slot="right">

```endpoints
   GET /:project/activity
   GET /:project/activity/:id
  POST /:project/activity/comment
 PATCH /:project/activity/comment/:id
DELETE /:project/activity/comment/:id
```

</info-box>
</two-up>

### Collections

<two-up>

::: slot left
Collections are the individual collections of items, similar to tables in a database.

Changes to collections will alter the schema of the database.

[Open collections reference](/api/collections.html)
:::

<info-box title="Endpoints" slot="right">

```endpoints
   GET /:project/collections
   GET /:project/collections/:collection
  POST /:project/collections
 PATCH /:project/collections/:collection
DELETE /:project/collections/:collection
```

</info-box>
</two-up>

### Collection Presets

<two-up>

::: slot left
Collection presets hold the preferences of individual users of the platform. This allows Directus to show and maintain custom item listings for users of the app.

[Open collection presets reference](/api/collection-presets.html)
:::

<info-box title="Endpoints" slot="right">

```endpoints
   GET /:project/collection_presets
   GET /:project/collection_presets/:id
  POST /:project/collection_presets
 PATCH /:project/collection_presets/:id
DELETE /:project/collection_presets/:id
```

</info-box>
</two-up>

### Extensions

<two-up>

::: slot left
Directus can easily be extended through the addition of several types of extensions, including layouts, interfaces, and modules.

[Open extensions reference](/api/extensions.html)
:::

<info-box title="Endpoints" slot="right">

```endpoints
   GET /interfaces
   GET /layouts
   GET /modules
```

</info-box>
</two-up>

### Fields

<two-up>

::: slot left
Fields are individual pieces of content within an item. They are mapped to columns in the database.

[Open field reference](/api/fields.html)
:::

<info-box title="Endpoints" slot="right">

```endpoints
   GET /:project/fields
   GET /:project/fields/:collection
   GET /:project/fields/:collection/:field
  POST /:project/fields/:collection
 PATCH /:project/fields/:collection/:field
DELETE /:project/fields/:collection/:field
```

</info-box>
</two-up>

### Folders

<two-up>

::: slot left
Folders don't do anything yet, but will be used in the (near) future to be able to group files.

[Open folders reference](/api/folders.html)
:::

<info-box title="Endpoints" slot="right">

```endpoints
   GET /:project/folders
   GET /:project/folders/:id
  POST /:project/folders
 PATCH /:project/folders/:id
DELETE /:project/folders/:id
```

</info-box>
</two-up>

### GraphQL

<two-up>

::: slot left
GraphQL provides a complete description of the data in your API, giving you the power to ask for exactly what you need, and nothing more.

[Open GraphQL reference](/api/GraphQL.html)
:::

<info-box title="Endpoints" slot="right">

```endpoints
  POST /:project/gql
```

</info-box>
</two-up>

### Mail

<two-up>

::: slot left
Send electronic mail through the electronic post.

[Open mail reference](/api/mail.html)
:::

<info-box title="Endpoints" slot="right">

```endpoints
  POST /:project/mail
```

</info-box>
</two-up>

### Permissions

<two-up>

::: slot left
Permissions control who has access to what and when.

[Open permissions reference](/api/permissions.html)
:::

<info-box title="Endpoints" slot="right">

```endpoints
   GET /:project/permissions
   GET /:project/permissions/:id
   GET /:project/permissions/me
   GET /:project/permissions/me/:collection
  POST /:project/permissions
 PATCH /:project/permissions/:id
DELETE /:project/permissions/:id
```

</info-box>
</two-up>

### Projects

<two-up>

::: slot left
Projects are the individual tenants of the platform. Each project has its own database and data.

[Open projects reference](/api/projects.html)
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

### Relations

<two-up>

::: slot left
What data is linked to what other data. Allows you to assign authors to articles, products to sales, and whatever other structures you can think of.

[Open relations reference](/api/relations.html)
:::

<info-box title="Endpoints" slot="right">

```endpoints
   GET /:project/relations
   GET /:project/relations/:id
  POST /:project/relations
 PATCH /:project/relations/:id
DELETE /:project/relations/:id
```

</info-box>
</two-up>

### Revisions

<two-up>

::: slot left
Revisions are individual changes to items made. Directus keeps track of changes made, so you're able to revert to a previous state at will.

[Open revisions reference](/api/revisions.html)
:::

<info-box title="Endpoints" slot="right">

```endpoints
   GET /:project/revisions
   GET /:project/revisions/:id
```

</info-box>
</two-up>

### Roles

<two-up>

::: slot left
Roles are groups of users that share permissions.

[Open roles reference](/api/roles.html)
:::

<info-box title="Endpoints" slot="right">

```endpoints
   GET /:project/roles
   GET /:project/roles/:id
  POST /:project/roles
 PATCH /:project/roles/:id
DELETE /:project/roles/:id
```

</info-box>
</two-up>

### SCIM

<two-up>

::: slot left
Directus partially supports Version 2 of System for Cross-domain Identity Management (SCIM). It is an open standard that allows for the exchange of user information between systems, therefore allowing users to be externally managed using the endpoints described below.

[Open SCIM reference](/api/scim.html)
:::

<info-box title="Endpoints" slot="right">

```endpoints
  POST /:project/scim/v2/Users
   GET /:project/scim/v2/Users
   GET /:project/scim/v2/Users/:id
 PATCH /:project/scim/v2/Users/:id
   GET /:project/scim/v2/Groups
   GET /:project/scim/v2/Groups/:id
 PATCH /:project/scim/v2/Groups/:id
DELETE /:project/scim/v2/Groups/:id
```

</info-box>
</two-up>

### Server

<two-up>

::: slot left
Access to where Directus runs. Allows you to make sure your server has everything needed to run the platform, and check what kind of latency we're dealing with.

[Open server reference](/api/server.html)
:::

<info-box title="Endpoints" slot="right">

```endpoints
   GET /server/info
   GET /server/ping
```

</info-box>
</two-up>

### Settings

<two-up>

::: slot left
Settings control the way the platform works and acts.

[Open settings reference](/api/settings.html)
:::

<info-box title="Endpoints" slot="right">

```endpoints
   GET /:project/settings
   GET /:project/settings/:id
  POST /:project/settings
 PATCH /:project/settings/:id
DELETE /:project/settings/:id
```

</info-box>
</two-up>

### Users

<two-up>

::: slot left
Users are what gives you access to the data. 

[Open users reference](/api/users.html)
:::

<info-box title="Endpoints" slot="right">

```endpoints
   GET /:project/users
   GET /:project/users/:id
   GET /:project/users/me
  POST /:project/users
 PATCH /:project/users/:id
DELETE /:project/users/:id
  POST /:project/users/invite
  POST /:project/users/invite/:token
 PATCH /:project/users/:id/tracking/page
   GET /:project/users/:id/revisions
   GET /:project/users/:id/revisions/:offset
```

</info-box>
</two-up>

### Utilities

<two-up>

::: slot left
Directus comes with various utility endpoints you can use to simplify your development flow.

[Open utilities reference](/api/utilities.html)
:::

<info-box title="Endpoints" slot="right">

```endpoints
  POST /:project/utils/hash
  POST /:project/utils/hash/match
  POST /:project/utils/random/string
   GET /:project/utils/2fa_secret
```

</info-box>
</two-up>