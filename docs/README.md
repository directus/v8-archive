---
sidebar: auto
---

# Directus API Reference

## Introduction

### Database connection
Something on database support

### Environments

All endpoints are being “prefixed” by the database name (as defined in the config file). The API will read all config files in order to connect to the right database for the current request.
A special character ( _ ) can be used to target the default database, whichever that is.
A few examples of api requests:
`/api/_/collections/` (use default config file config.php)
`/api/prod/items/projects` (use prod config file config.prod.php)
Note: the name in the API URL is not the name of the database itself.

### Response format

All output will adhere to the same general JSON structure:

```json
{
  "error": {
    "code": [Number],
    "message": [String],
  },
  "data": [Object | Array],
  "meta": [Object]
}
```



## Authentication

### Tokens

### Users

### Server-use Tokens


## Items

### Get Items

Gets the items from a given collection.

```http
/items/<collection>
```

#### Supported parameters

| param | description                      | default value |
|-------|----------------------------------|---------------|
| limit | The number of records to request | 20            |
| sort  | What field to sort by            | id            |
| q     | String to search by              | --            |

#### Examples

* Search for all projects in the `design` category
  ```bash
  curl -g https://demo-api.getdirectus.com/_/items/projects?filter[category][eq]=design
  ```

::: tip
This is tip message
:::

::: warning
This is a warning
:::

::: danger
This is a danger Note
:::

::: danger STOP
This is danger note with a custom title
:::
