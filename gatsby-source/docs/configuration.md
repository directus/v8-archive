# Configuration

- [Authentication](#authentication)
- [Options Schema](#options-schema)
- [Examples](#examples)

## Authentication

It's recommended to set up a separate Directus user dedicated for usage with this plugin. Make sure you grant `read` privileges to the user on all tables, including system tables. See more in the [Directus docs](https://docs.directus.io/guides/permissions.html#collection-level).

We provide two authentication strategies, configured through the `auth` property of the `options` object.

Because the plugin requires `read` access to all Directus tables, it's important to ensure proper steps are taken to secure the authentication information provided. We advise setting `auth` values using environment variables (see [dotenv](https://github.com/motdotla/dotenv) and [ this blog post ](https://danlevy.net/protect-your-tokens/#-secret-keys) for more).

### Token based authentication

This is the **recommended** authentication strategy. A user token is provided to the plugin through the [`auth.token`](#authtoken) property. The plugin then uses this authentication token to make requests to the Directus API.

### User credential based authentication

This strategy uses a traditional email and password combination. The email is provided via [`auth.email`](#authemail) and the corresponding password is provided via [`auth.password`](#authpassword). The plugin will login as this user when making requests to the Directus API.

**Note:** If a `auth.token` value is provided, these fields are **ignored**, and the token is used instead.

### Public authentication

This strategy is **not** recommended. If no `auth.token` or `auth.(email|password)` values are provided, the plugin will attempt to use the public API. This strategy will only work if you've granted `read` access to all public users.

## Options Schema

Find details regarding the `options` object schema below. Required fields are denoted with an `*`.

- [`url*`](#url-required)
- [`project*`](#project-required)
- [`auth.token`](#authtoken)
- [`auth.email`](#authemail)
- [`auth.password`](#authpassword)
- [`targetStatuses`](#targetstatuses)
- [`allowCollections`](#allowcollections)
- [`blockCollections`](#blockcollections)
- [`typePrefix`](#typeprefix)
- [`includeJunctions`](#includejunctions)
- [`downloadFiles`](#downloadfiles)
- [`customRecordFilter`](#customrecordfilter)

See some [examples](#examples).

### `url` **(required)**

Base URL for the Directus API.

```js
field: "url"
type: string
default:
```

### `project` **(required)**

Target project name for the Directus API.

```js
field: "project"
type: string
default:
```

### `auth.token`

A user access token for the Directus API.

See the [authentication](#authentication) section for more.

```js
field: "auth.token"
type: string | void
default: void
```

### `auth.email`

User email used to log in to the Directus API.

```js
field: "auth.email"
type: string | void
default: void
```

### `auth.password`

User password used to log in to the Directus API.

```js
field: "auth.password"
type: string | void
default: void
```

### `targetStatuses`

A set of allowed statuses to **include** in the content mesh.

A value of `void` includes items with any status.

The string **'\_\_NONE\_\_'** can be used to includes items with no status defined.

```js
field: "targetStatuses"
type: string[] | void
default: ['published', '__NONE__']
```

### `allowCollections`

A set of allowed collection names to **include** in the content mesh.

A value of `void` includes all collections.

```js
field: "allowCollections"
type: string[] | void
default: void
```

### `blockCollections`

A set of blocked collection names to **exclude** from the content mesh.

A value of `void` does not exclude any collections.

```js
field: "blockCollections"
type: string[] | void
default: void
```

### `typePrefix`

Prefix to use for the node types exposed in the GraphQL layer.

```js
field: "typePrefix"
type: string
default: "Directus"
```

### `includeJunctions`

Whether to include junction tables that manage M2M relations in the GraphQL layer.

```js
field: "includeJunctions"
type: boolean
default: false
```

### `downloadFiles`

Whether to download the files to the disk.

Enables images to be used with other transform plugins.

Setting to `false` could be useful if the project has many files.

```js
field: "downloadFiles"
type: boolean
default: true
```

### `customRecordFilter`

A function executed for each record, returning whether the record should
be included in the content mesh.

If provided, this will **override** any `targetStatuses` value.

```js
field: "customRecordFilter"
type: ((record: any, collection: string) => boolean) | void
default: void
```

## Examples

Basic configuration including only published or statusless items.

```js
// gatsby-config.js

module.exports = {
  // ...
  plugins: [
    {
      // ...
      resolve: '@directus/gatsby-source-directus',
      options: {
        url: 'https://directus.example.com',
        project: '_',
        auth: {
          // Note: you should extract the login information
          // to environment variables.
          email: 'admin@example.com',
          password: 'example',
        },
        targetStatuses: ['published', '__NONE__'],
      },
      // ...
    },
  ],
  // ...
};
```

Basic configuration using [token based](#authentication) authentication passed via process variables. Note the `DIRECTUS_API_TOKEN` would need to be set manually using something like [dotenv](https://github.com/motdotla/dotenv).

```js
// gatsby-config.js

module.exports = {
  // ...
  plugins: [
    {
      // ...
      resolve: '@directus/gatsby-source-directus',
      options: {
        url: 'https://directus.example.com',
        project: '_',
        auth: {
          token: process.env.DIRECTUS_API_TOKEN,
        },
        targetStatuses: ['published', '__NONE__'],
      },
      // ...
    },
  ],
  // ...
};
```

Custom configuration including published, draft, or statusless items. Blocks the `super_secret_stuff` collection, and will **not** download files for processing by `gatsby-image`.

```js
// gatsby-config.js

module.exports = {
  // ...
  plugins: [
    {
      // ...
      resolve: '@directus/gatsby-source-directus',
      options: {
        url: 'https://directus.example.com',
        project: '_',
        auth: {
          // Note: you should extract the login information
          // to environment variables.
          token: 'admin@example.com',
          password: 'example',
        },
        targetStatuses: ['published', 'draft', '__NONE__'],
        blockCollections: ['super_secret_stuff'],
        downloadFiles: false,
      },
      // ...
    },
  ],
  // ...
};
```
