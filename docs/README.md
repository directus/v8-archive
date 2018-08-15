---
sidebar: auto
---

# Directus API Reference

## Introduction

### Versioning

The Directus API uses SemVer for version labeling within the repo and for files which mention a specific version (eg: `package.json`). The API will _not_ include the version in the URL because the API is "versionless". Being versionless means that we will not remove or change API behavior, only _adding_ new features and enhancements – therefore, no breaking changes will ever be introduced.

### Environments

All endpoints are prefixed with the an environment name (based on a configuration file name). The API will try to find a configuration file that matches a given environment name and use it as the request configuration.
The underscore (`_`) is reserved as the default environment name.

A few examples of api requests when your api is located at `/api` sub-directory:

*   `/api/_/collections` (uses default config file `config.php`)
*   `/api/prod/items/projects` (uses prod config file `config.prod.php`)

::: tip
The naming format of the configuration file is `config.<environment-name>.php`
:::

### Response Format

All output will adhere to the same general JSON structure:

```json
{
    "error": {
        "code": [Number],
        "message": [String]
    },
    "data": [Object | Array],
    "meta": [Object]
}
```

### HTTP Status Codes

The API uses HTTP status codes in addition to the message value. Everything in the 200 range is a valid response. The API does not serve translated error messages based on locale.

| Code | Description           |
| ---- | --------------------- |
| 200  | OK                    |
| 201  | Created               |
| 204  | No Content            |
| 400  | Bad Request           |
| 401  | Unauthorized          |
| 403  | Forbidden             |
| 404  | Not Found             |
| 409  | Conflict              |
| 422  | Unprocessable Entity  |
| 500  | Internal Server Error |

The `error` property is only present when an error has occurred.

### Error codes

#### General error codes

- **0000** - Internal Error (500)
- **0001** - Not Found (404)
- **0002** - Bad Request (400)
- **0003** - Unauthorized (401)
- **0004** - Invalid Request (400) (_Validation_)
- **0005** - Endpoint Not Found (404)
- **0006** - Method Not Allowed (405)
- **0007** - Too Many Requests (429)
- **0008** - API Environment Configuration Not Found (404)
- **0009** - Failed generating a SQL Query (500)
- **0010** - Forbidden (403)
- **0011** - Failed to connect to the database (500)
- **0012** - Unprocessable Entity (422)
- **0013** - Invalid or Empty Payload (400)
- **0014** - Default Instance not configured properly (503)

#### Authentication error codes

- **0100** - Invalid Credentials (404)
- **0101** - Invalid Token (401)
- **0102** - Expired Token (401)
- **0103** - Inactive User (401)
- **0104** - Invalid Reset Password Token (401)
- **0105** - Expired Reset Password Token (401)
- **0106** - User Not Found (404)
- **0107** - User with a given email Not Found (404)
- **0108** - User not authenticated (401)

#### Items error codes

- **0200** - Collection Not Found (404)
- **0201** - Not Allow Direct Access To System Table (401)
- **0202** - Field Not Found (404)
- **0203** - Item Not Found (404)
- **0204** - Duplicate Item (409)
- **0205** - Collection not being managed by Directus
- **0206** - Field not being managed by Directus
- **0207** - Revision Not Found (404)
- **0208** - Revision has an invalid delta
- **0209** - Field Invalid (400) - Trying to use a field that doesn't exists for actions such as filtering and sorting
- **0210** - Cannot add a comment to an item
- **0211** - Cannot edit a comment from an item
- **0212** - Cannot delete a comment from an item

#### Collections error codes

- **0300** - Reading items denied (403)
- **0301** - Creating items denied (403)
- **0302** - Updating items denied (403)
- **0303** - Deleting items denied (403)
- **0304** - Reading field denied (403)
- **0305** - Writing to field denied (403)
- **0306** - Altering collection was denied (403)
- **0307** - Collection already exists (422)
- **0308** - Field already exists (422)
- **0309** - Unable to find items owned by an specific user (403) 

#### Schema error codes

- **0400** - Unknown Error (500)
- **0401** - Unknown data type (400)

#### Mail error codes

- **0500** - Mailer Transport not found (500)
- **0501** - Invalid Transport option (500)
- **0502** - Invalid Transport instance (500)

#### Filesystem error codes

- **0600** - Unknown Error (500)
- **0601** - The uploaded file exceeds max upload size that was specified on the server (500)
- **0602** - The uploaded file exceeds the max upload size that was specified in the client (500)
- **0603** - The uploaded file was only partially uploaded (500)
- **0604** - No file was uploaded (500)
- **0605** - _Not defined yet_
- **0606** - Missing temporary upload folder (500)
- **0607** - Failed to write file to disk (500)
- **0608** - A PHP extension stopped the file upload (500)

#### Utils error codes

- **1000** - Hasher not found (400)

### Validation

The API performs two types of validation on submitted data:

*   **Data Type** – The API checks the submitted value's type against the directus or database's field type. For example, a String submitted for an INT field will result in an error.
*   **RegEx** – The API checks the submitted value against its column's `directus_fields.validation` RegEx. If the value doesn't match then an error will be returned.

## Authentication

Most endpoints are checked against the permissions settings. If a user is not authenticated or isn’t allowed to access certain endpoints then API will respond with either a `401 Unauthorized` or a `403 Forbidden` respectively. In addition to these status codes, the API returns a specific reason in the `error.message` field.

### Tokens

To gain access to protected data, you must include an access token with every request. These tokens follow the [JWT spec](https://jwt.io) and contain the user id, type of token (`auth`), and an expiration date in the payload, which is encrypted with a secret key.

There are several ways to include this access token:

#### 1. Bearer Token in Authorization Header

`curl -H "Authorization: Bearer Py8Rumu.LD7HE5j.uFrOR5" https://example.com/api/`

#### 2. HTTP Basic Auth

`curl -u Py8Ru.muLD7HE.5juFrOR5: https://example.com/api/`

Notice that the token is `Py8Ru.muLD7HE.5juFrOR5` and has a colon `:` at the end. Using the Basic auth, the auth user is the token and the auth password should be either blank or the same token.

#### 3. Query `access_token` Parameter

`curl https://example.com/api/?access_token=Py8RumuLD.7HE5j.uFrOR5`

### Get Authentication Token

Gets a token from a Directus user's credentials

```http
POST /[env]/auth/authenticate
```

#### Body

The users credentials

```json
{
    "email": "rijk@directus.io",
    "password": "supergeheimwachtwoord"
}
```

#### Common Responses

| Code            | Description                        |
| --------------- | ---------------------------------- |
| 200 OK          | `data: [new access token]`         |
| 400 Bad Request | `message: wrong email or password` |

::: warning
The access token that is returned through this endpoint must be used with any subsequent requests except for endpoints that don’t require auth.
:::


#### Protected endpoints

| Endpoint                   | Protected
| -------------------------- | -----------------------
| /[env]/                    | Yes
| /[env]/activity            | Yes
| /[env]/auth                | No
| /[env]/collections         | Yes
| /[env]/collection_presets  | Yes
| /[env]/custom              | No
| /[env]/fields              | Yes
| /[env]/files               | Yes
| /[env]/items               | Yes
| /[env]/interfaces          | No
| /[env]/mail                | Yes
| /[env]/pages               | No
| /[env]/permissions         | Yes
| /[env]/relations           | Yes
| /[env]/revisions           | Yes
| /[env]/roles               | Yes
| /[env]/scim/v2             | Yes
| /[env]/settings            | Yes
| /[env]/users               | Yes
| /[env]/utils               | Yes
| /                          | Yes
| /instances                 | No
| /interfaces                | Yes
| /layouts                   | Yes
| /pages                     | Yes
| /server                    | Yes
| /types                     | Yes

### Create new instance

Create a new instance connection

```http
POST /instances
```

#### Body

| Attribute       | Description                            | Required
| --------------- | -------------------------------------- | ---------
| `db_host`       | Database host. Default: `localhost`    | No
| `db_port`       | Database port. Default: `3306`         | No
| `db_name`       | Database name.                         | Yes
| `db_user`       | Database username.                     | Yes
| `db_password`   | Database user password. Default: `None`| No
| `user_email`    | Admin email                            | Yes
| `user_password` | Admin password                         | Yes
| `user_token`    | Admin token. Default: `admin_token`    | No
| `mail_from`     | Default mailer `from` email            | No
| `project_name`  | The Directus name. Default: `Directus` | No
| `env`           | The environment name.                  | No
| `force`         | Force the installation                 | No
| `cors_enabled`  | Enable CORS                            | No 

::: warning
When `env` is not specified it will create the default configuration
:::

```json
{
    "db_name": "directus",
    "db_user": "root",
    "db_password": "pass",
    "user_email": "admin@admin.com",
    "user_password": "admin"
}
```

### Refresh Authentication Token

Gets a new fresh token using a valid auth token

```http
POST /[env]/auth/refresh
```

#### Body

A valid token

```json
{
    "token": "123abc456def"
}
```

#### Common Responses

| Code            | Description                |
| --------------- | -------------------------- |
| 200 OK          | `data: [new access token]` |
| 400 Bad Request | `message: invalid token`   |

::: warning
The access token that is returned through this endpoint must be used with any subsequent requests except for endpoints that don’t require auth.
:::

### Password Reset Request

The API will send an email to the requested user’s email containing a link with a short-lived reset token. This reset token can be used to finish the password reset flow.

The reset token is a JWT token that include the user id, email and expiration time.

```http
POST /[env]/auth/password/request
```

#### Body

The user's email address and the app URL from which the reset is requested

```json
{
    "email": "rijk@directus.io",
    "instance": "https://example.com/admin/"
}
```

#### Common Responses

| Code   | Description                                                                |
| ------ | -------------------------------------------------------------------------- |
| 200 OK | Always returns success to avoid malicious checks for valid email addresses |

### Password Reset

The API checks the validity of the reset token, that it hasn't expired, and matches the encrypted email address contained in the code to the one provided. It must be a GET request, since we can’t do POST requests from email clients. This endpoint generates a random temporary password for the user and sends it to their email address.

```http
GET /[env]/auth/password/reset/[reset-token]
```

#### Common Responses

| Code   | Description                                                                |
| ------ | -------------------------------------------------------------------------- |
| 401    | Invalid/Expired token |

### Get a list of Single Sign-On services

```http
GET /[env]/auth/sso
```

A list of Third-party authentication services, such as Google and Facebook.

### Redirect to the authorization page

```http
GET /auth/sso/[provider]
```

Automatically redirects to the authorization url if the origin host is allowed by the API otherwise it will return the authorization url.

### Authenticate using the OAuth token/code

::: warning
This endpoint is only useful when the callback is not handled by the API. See: /[env]/auth/sso/[provider]/callback.
:::

When the server authorized the user after authenticated it returns a `oauth_token` and `oauth_verifier` (version 1.0) or `code` (version 2.0).

```http
POST /[env]/auth/sso/[provider]
```

#### Body

The user's email address and the app URL from which the reset is requested

##### OAuth 1.0
```json
{
    "oauth_token": "[oauth-token]",
    "oauth_verifier": "[oauth-verifier]"
}
```

##### OAuth 2.0
```json
{
    "code": "[verification-code]"
}
```

### Single Sign-On Authorization callback

```http
GET /[env]/auth/sso/[provider]/callback
```

Set this url as the callback for the OAuth service and it will return a "request token" that the client can use to request the access token.

### Get Access Token using the Request Token

```http
POST /[env]/auth/sso/access_token
```

Using the request token that was returned by the `/[env]/auth/sso/[provider]/callback` endpoint to get the access token.

#### Body

```json
{
    "request_token": "<request-token>"
}
```

## Parameters

There are many common query parameters used throughout the API. Those are described here and referenced from within each endpoint's section.

### Sorting

`sort` is a CSV of fields used to sort fetched items. Sorting defaults to ascending (ASC) but a minus sign (`-`) can be used to reverse this to descending (DESC). Fields are prioritized by their order in the CSV. You can use a `?` to sort by random.

#### Examples

*   `sort=?` Sorts randomly
*   `sort=name` Sorts by `name` ASC
*   `&sort=name,-age` Sorts by `name` ASC followed by `age` DESC
*   `sort=name,-age,?` Sorts by `name` ASC followed by `age` DESC, followed by random

### Fields

`fields` is a CSV of columns to include in the output. This parameter supports dot notation to request nested relational fields. You can also use a wildcard (`*`) for "everything".

#### Examples

*   `fields=*` Gets all top-level fields
*   `fields=*.*` Gets all top-level fields and all relational fields one-level deep
*   `fields=*,images.*` Gets all top-level fields and all relational fields within `images`
*   `fields=first_name,last_name` Gets only the `first_name` and `last_name` fields
*   `fields=*.*,images.thumbnails.*` Get all fields for top level and one level deep, as well as three levels deep within `images.thumbnails`

### Filtering

Used to fetch specific items from a collection based on one or more filters. Filters follow the syntax `filter[<field-name>][<operator>]=<value>`. The field-name supports dot notation to filter on nested relational fields.

#### Filter Operators

| Operator             | Description                            |
| -------------------- | -------------------------------------- |
| `=`, `eq`            | Equal to                               |
| `<>`, `!=`, `neq`    | Not Equal to                           |
| `<`, `lt`            | Less than                              |
| `<=`, `lte`          | Less than or equal to                  |
| `>`, `gt`            | Greater than                           |
| `>=`, `gte`          | Greater than or equal to               |
| `in`                 | One of these                           |
| `nin`                | Not one of these                       |
| `null`               | Is null                                |
| `nnull`              | Is not null                            |
| `contains`, `like`   | Contains the substring                 |
| `ncontains`, `nlike` | Doesn't contain this substring         |
| `between`            | Is between                             |
| `nbetween`           | Is not between                         |
| `empty`              | Is empty (null or falsy value)         |
| `nempty`             | Is not empty (null or falsy value)     |
| `all`                | Match all related items @TODO: Clarify |
| `has`                | Has one or more related items          |

#### AND vs OR

By default, all chained filters are treated as ANDs. To create an OR combination, you can add the `logical` operator like follows:

```
GET /items/projects?filter[category][eq]=development&filter[title][logical]=or&filter[title][like]=design
```

::: tip
In nearly all cases, it makes more sense to use the `in` operator instead of going with the logical-or. For example, the above example can be rewritten as

```
GET /items/projects?filter[category][in]=development,design
```

:::

#### Filtering by Date/DateTime

```
# Equal to
GET /items/comments?filter[datetime]=2018-05-21 15:48:03

# Greater than
GET /items/comments?filter[datetime][gt]=2018-05-21 15:48:03

# Greater than or equal to
GET /items/comments?filter[datetime][gte]=2018-05-21 15:48:03

# Less than
GET /items/comments?filter[datetime][lt]=2018-05-21 15:48:03

# Less than or equal to
GET /items/comments?filter[datetime][lte]=2018-05-21 15:48:03

# Between two date
GET /items/comments?filter[datetime][lte]=2018-05-21 15:48:03,2018-05-21 15:49:03
```

::: warning
Date should follow the `YYYY-MM-DD` format. Ex: 2018-01-01
Time should follow the `HH:mm:ss` format. Ex: 15:01:01
:::

### Metadata

`meta` is a CSV of metadata fields to include. This parameter supports the wildcard (`*`) to return all metadata fields.

#### Options

*   `result_count` - Number of items returned in this response
*   `total_count` - Total number of items in this collection
*   `status` - Collection item count by statuses
*   `collection` - The collection name
*   `type`
    *   `collection` if it is a collection of items
    *   `item` if it is a single item

### Language

`lang` is a CSV of languages that should be returned with the response. This parameter can only be used when a Translation field has been included in the collection. This parameter supports the wildcard (`*`) to return all translations.

### Search Query

`q` is a search query that will perform a filter on all string-based fields within the collection (see list below). It's an easy way to search for an item without creating complex field filters – though it is far less optimized.

#### Searched Datatypes

* `CHAR`
* `VARCHAR`
* `TINYTEXT`
* `TEXT`
* `MEDIUMTEXT`
* `LONGTEXT`
* `TINYJSON`
* `JSON`
* `MEDIUMJSON`
* `LONGJSON`
* `ARRAY`
* `CSV`
* `UUID`
* `TINYINT`
* `SMALLINT`
* `INTEGER`
* `INT`
* `MEDIUMINT`
* `BIGINT`
* `SERIAL`
* `FLOAT`
* `DOUBLE`
* `DECIMAL`
* `REAL`
* `NUMERIC`
* `BIT`
* `BOOL`
* `BOOLEAN`

## Items

Items are essentially individual database records which each contain one or more fields (database columns). Each item belongs to a specific container (database table) and is identified by the value of its primary key field. In this section we describe the different ways you can manage items.

### Create Item

Creates one or more items in a given collection

```http
POST /items/[collection-name]
```

#### Body

A single item or an array of multiple items to be created. Field keys must match the collection's column names.

##### One Item (Regular)

```json
{
    "title": "Project One",
    "category": "Design"
}
```

##### Multiple Items (Batch)

```json
[
    {
        "title": "Project One",
        "category": "Design"
    },
    {
        "title": "Project Two",
        "category": "Development"
    }
]
```

#### Common Responses

| Code                     | Description                                                             |
| ------------------------ | ----------------------------------------------------------------------- |
| 201 Created              | `data`: The created item(s), including default fields added by Directus |
| 400 Bad Request          | `message`: Syntax error in provided JSON                                |
| 404 Not Found            | `message`: Collection doesn’t exist                                     |
| 400 Bad Request          | `message`: Invalid request body                                         |
| 422 Unprocessable Entity | `message`: Field doesn’t exist in collection                            |

::: tip
The API may not return any data for successful requests if the user doesn't have adequate read permission. `204 NO CONTENT` is returned instead.
:::

### Get Item

Get one or more single items from a given collection

```http
GET /items/[collection-name]/[pk]
GET /items/[collection-name]/[pk],[pk],[pk]
```

#### Query Parameters

| Name   | Default   | Description                                                |
| ------ | --------- | ---------------------------------------------------------- |
| fields | \*        | CSV of fields to include in response [Learn More](#fields) |
| meta   |           | CSV of metadata fields to include [Learn More](#metadata)  |
| status | Published | CSV of statuses [Learn More](#status)                      |
| lang   | \*        | Include translation(s) [Learn More](#language)             |

#### Common Responses

| Code          | Description                                                                  |
| ------------- | ---------------------------------------------------------------------------- |
| 200 OK        | `data`: Retrieved item<br>`meta`: Depends on requested metadata              |
| 404 Not Found | `message`: Collection doesn’t exist, or item doesn't exist within collection |

#### Examples

*   Return the project item with an ID of `1`
    ```bash
    curl -u <token>: https://api.directus.io/_/items/projects/1
    ```
    *   Return project items with IDs of `1`, `3`, `11`
    ```bash
    curl -u <token>: https://api.directus.io/_/items/projects/1,3,11
    ```

### Get Items

Get an array of items from a given collection

```http
GET /items/[collection-name]
```

#### Query Parameters

| Name          | Default   | Description                                                |
| ------------- | --------- | ---------------------------------------------------------- |
| limit         | 20        | The number of items to request                             |
| offset        | 0         | How many items to skip before fetching results             |
| sort          | [pk]      | CSV of fields to sort by [Learn More](#sorting)            |
| fields        | \*        | CSV of fields to include in response [Learn More](#fields) |
| filter[field] |           | Filter items using operators [Learn More](#filtering)      |
| meta          |           | CSV of metadata fields to include [Learn More](#metadata)  |
| status        | Published | CSV of statuses [Learn More](#status)                      |
| lang          | \*        | Include translation(s) [Learn More](#language)             |
| q             |           | Search string [Learn More](#search-query)                  |
| joins         |           | Join two or more tables @TODO examples                     |
| group         |           | Group items by a field value @TODO examples                |

#### Common Responses

| Code          | Description                                                     |
| ------------- | --------------------------------------------------------------- |
| 200 OK        | `data`: Array of items<br>`meta`: Depends on requested metadata |
| 404 Not Found | `message`: Collection doesn’t exist                             |

#### Examples

*   Search for all projects in the `design` category
    ```bash
    curl -u [token]: -g https://api.directus.io/_/items/projects?filter[category][eq]=design
    ```

### Get Item Revision

Get a specific revision from a given item. This endpoint uses a zero-based offset to select a revision, where `0` is the creation revision. Negative offsets are allowed, and select as if `0` is the current revisions.

```http
GET /items/[collection-name]/[pk]/revisions/[offset]
```

#### Query Parameters

| Name   | Default   | Description                                                |
| ------ | --------- | ---------------------------------------------------------- |
| fields | \*        | CSV of fields to include in response [Learn More](#fields) |
| meta   |           | CSV of metadata fields to include [Learn More](#metadata)  |
| status | Published | CSV of statuses [Learn More](#status)                      |
| lang   | \*        | Include translation(s) [Learn More](#language)             |

#### Common Responses

| Code          | Description                                                                  |
| ------------- | ---------------------------------------------------------------------------- |
| 200 OK        | `data`: Retrieved item<br>`meta`: Depends on requested metadata              |
| 404 Not Found | `message`: Collection doesn’t exist, or item doesn't exist within collection |

#### Examples

*   Return the 2nd revision (from creation) for the project item with a primary key of 1
    ```bash
    curl -u <token>: https://api.directus.io/_/items/projects/1/revisions/2
    ```
*   Return the 2nd from current revision for the project item with a primary key of 1
    ```bash
    curl -u <token>: https://api.directus.io/_/items/projects/1/revisions/-2
    ```

### Get Item Revisions

Get an array of revisions from a given item

```http
GET /items/[collection-name]/[pk]/revisions
```

#### Query Parameters

| Name          | Default   | Description                                                |
| ------------- | --------- | ---------------------------------------------------------- |
| limit         | 200       | The number of items to request                             |
| offset        | 0         | How many items to skip before fetching results             |
| fields        | \*        | CSV of fields to include in response [Learn More](#fields) |
| meta          |           | CSV of metadata fields to include [Learn More](#metadata)  |
| lang          | \*        | Include translation(s) [Learn More](#language)             |

#### Common Responses

| Code          | Description                                                     |
| ------------- | --------------------------------------------------------------- |
| 200 OK        | `data`: Array of items<br>`meta`: Depends on requested metadata |
| 404 Not Found | `message`: Collection doesn’t exist                             |

#### Examples

*   Get all revisions from the project item with a primary key of 1
    ```bash
    curl https://api.directus.io/_/items/projects/1/revisions
    ```

### Update Item

Update or replace a single item from a given collection

@TODO LOOK INTO ALLOWING FILTER PARAM FOR UPDATES, EG: `PUT /items/projects?filter[title][eq]=title`

```http
PATCH /items/[collection-name]/[pk]
```

::: warning

*   **PATCH** partially updates the item with the provided data, any missing data is ignored

:::

#### Body

A single item to be updated. Field keys must match the collection's column names

#### Common Responses

| Code                     | Description                                                          |
| ------------------------ | -------------------------------------------------------------------- |
| 200 OK                   | `data`: The updated item, including default fields added by Directus |
| 400 Bad Request          | `message`: Syntax error in provided JSON                             |
| 404 Not Found            | `message`: Collection doesn’t exist                                  |
| 422 Unprocessable Entity | `message`: Column doesn’t exist in collection                        |

#### Examples

*   Return the project item with an ID of `1`
    ```bash
    curl -u <token>: https://api.directus.io/_/items/projects/1
    ```

### Update Items

Update multiple items in a given collection

```http
PATCH /items/[collection-name]
```

::: warning PATCH

*   **PATCH** partially updates the item with the provided data, any missing data is ignored

:::

::: danger WARNING
Batch Update can quickly overwrite large amounts of data. Please be careful when implementing this request.
:::

#### Common Responses

| Code                     | Description                                                          |
| ------------------------ | -------------------------------------------------------------------- |
| 200 OK                   | `data`: The updated item, including default fields added by Directus |
| 400 Bad Request          | `message`: Syntax error in provided JSON                             |
| 404 Not Found            | `message`: Collection doesn’t exist                                  |
| 422 Unprocessable Entity | `message`: Column doesn’t exist in collection                        |

### Revert Item

Reverts a single item to a previous revision state

```http
PATCH /items/[collection-name]/[item-pk]/revert/[revision-pk]
```

#### Body

There is no body for this request

#### Common Responses

| Code                     | Description                                                          |
| ------------------------ | -------------------------------------------------------------------- |
| 200 OK                   | `data`: The updated item, including default fields added by Directus |
| 404 Not Found            | `message`: Collection doesn’t exist                                  |
| 422 Unprocessable Entity | `message`: Item doesn’t exist in collection                          |

#### Examples

*   Revert the project item (ID:`1`) to its previous state in revision (ID:`2`)
    ```bash
    curl -u <token>: https://api.directus.io/_/items/projects/1/revert/2
    ```

### Delete Item

Deletes one or more items from a specific collection. This endpoint also accepts CSV of primary key values, and would then return an array of items

```http
DELETE /items/[collection-name]/[pk]
DELETE /items/[collection-name]/[pk],[pk],[pk]
```

#### Common Responses

| Code           | Description                                     |
| -------------- | ----------------------------------------------- |
| 204 No Content | Record was successfully deleted                 |
| 404 Not Found  | `message`: Item doesn't exist within collection |

::: danger WARNING
Batch Delete can quickly destroy large amounts of data. Please be careful when implementing this request.
:::

## System

@TODO All these endpoints need to have the same reference as listed above

All system tables (`directus_*`) are blocked from being used through the regular `/items` endpoint to prevent security leaks or because they require additional processing before sending to the end user. This means that any requests to `/items/directus_*` will always return `401 Unauthorized`.

These system endpoints still follow the same spec as a “regular” `/items/[collection-name]` endpoint but require the additional processing outlined below:

### Activity

#### Activities Type
| Name          | Description                                                   |
| ------------- | ------------------------------------------------------------- |
| ENTRY         | Activities to any items besides files and settings collection |
| FILES         | Activities on the Directus files collection                   |
| SETTINGS      | Activities on the Directus settings collection                |
| LOGIN         | Activities on authentication                                  |
| COMMENT       | Activities related to a comment in a collections's item       |

#### Activities Actions
| Name          | Description                                                |
| ------------- | ---------------------------------------------------------- |
| ADD           | Item created                                               |
| UPDATE        | Item updated                                               |
| DELETE        | Item deleted                                               |
| SOFT_DELETE   | Item soft-deleted. Update to a soft delete status          |
| LOGIN         | User authenticate using credentials                        |
| REVERT        | Item updated using a revision data                         |

#### Get activities

Get an array of activities

```http
GET /activity
```

##### Query Parameters

| Name          | Default   | Description                                                |
| ------------- | --------- | ---------------------------------------------------------- |
| limit         | 20        | The number of items to request                             |
| offset        | 0         | How many items to skip before fetching results             |
| sort          | id        | CSV of fields to sort by [Learn More](#sorting)            |
| fields        | \*        | CSV of fields to include in response [Learn More](#fields) |
| filter[field] |           | Filter items using operators [Learn More](#filtering)      |
| meta          |           | CSV of metadata fields to include [Learn More](#metadata)  |
| q             |           | Search string [Learn More](#search-query)                  |
| joins         |           | Join two or more tables @TODO examples                     |
| group         |           | Group items by a field value @TODO examples                |


#### Get Item

Get one or more single items from a given collection

```http
GET /activity/[pk]
```

##### Query Parameters

| Name   | Default   | Description                                                |
| ------ | --------- | ---------------------------------------------------------- |
| fields | \*        | CSV of fields to include in response [Learn More](#fields) |
| meta   |           | CSV of metadata fields to include [Learn More](#metadata)  |
| status | Published | CSV of statuses [Learn More](#status)                      |
| lang   | \*        | Include translation(s) [Learn More](#language)             |

##### Common Responses

| Code          | Description                                                                  |
| ------------- | ---------------------------------------------------------------------------- |
| 200 OK        | `data`: Retrieved item<br>`meta`: Depends on requested metadata              |
| 404 Not Found | `message`: Collection doesn’t exist, or item doesn't exist within collection |


#### Create a new message

Create a new message, needs to be related to an a collection.

```http
POST /activity/comment
```

##### Body

A single object representing the new comment.

```json
{
    "comment": "A new comment"
}
```

### Fields

These endpoints are used for creating, updating, or deleting fields through the API requires the API to modify the database schema directly.

#### List of fields

```http
GET /[env]/fields/[collection]
```

Returns the list of fields in a given collection.

#### Single field

```http
GET /[env]/fields/[collection]/[field]
```

Returns the details of a single field.

#### Create a new field

```http
POST /[env]/fields/[collection]
```

Creates a new field in a given collection.

#### Update field

```http
PATCH /[env]/fields/[collection]/[field]
```

Updates the details of a given field.

#### Delete field

```http
DELETE /[env]/fields/[collection]
```

Permanently deletes a field and its content.

### Files

These endpoints are used for creating or updating a files requires the API to accept a special field allowing for the base64 file data. Beyond that, it accepts POST requests with the multipart-formdata enctype, to allow for easier uploading of file(s).

#### List of files

```http
GET /[env]/files
```

Returns the list of your files.

#### Single file

```http
GET /[env]/files/[pk]
```

Returns the details of a single file.

#### Upload a new file

```http
POST /[env]/files
```

Uploads a new file.

#### Update file

```http
PATCH /[env]/files/[pk]
```

Updates the details of a given field.

#### Delete file

```http
DELETE /[env]/files/[pk]
```

Permanently deletes a file.

#### List of Revisions

```http
GET /[env]/files/[pk]/revisions
```

Returns a list of a single file revisions

#### Single revision

```http
GET /[env]/files/[pk]/revisions/[offset]
```

Returns the revision of a single item using a 0-index based offset.

#### Revert detail

```http
GET /[env]/files/[pk]/revert/[revision-pk]
```

Reverts the details of a file to a given revision.

### Folders

These endpoints are used for creating, updating, or deleting a virtual folder.

#### List of folders

```http
GET /[env]/files/folders
```

Returns the list of your virtual folders.

#### Single folder

```http
GET /[env]/files/folders/[pk]
```

Returns the details of a single virtual folder.

#### Create a new folder

```http
POST /[env]/files/folders
```

Creates a new virtual folder.

#### Update file

```http
PATCH /[env]/files/folders/[pk]
```

Updates the details of a given folder.

#### Delete file

```http
DELETE /[env]/files/[pk]
```

Permanently deletes a virtual folder. Leaving its sub folder and files orphan.

### Collections Presets

These endpoints are used for creating, updating, or deleting collection presets through the API requires the API to modify the database schema directly.

#### List of collection presets

```http
GET /[env]/collection_presets
```

Returns the list of collection presets

#### Single collection preset

```http
GET /[env]/collection_presets/[pk]
```

Returns the details of a single collection preset.

#### Create a new collection preset

```http
POST /[env]/collection_presets
```

Creates a new collection preset

#### Update a collection preset

```http
PATCH /[env]/collection_presets/[pk]
```

Updates the details of a given collection preset.

#### Delete collection preset

```http
DELETE /[env]/collection_presets/[pk]
```

Permanently deletes a collection_presets

### Permissions

These endpoints are used for creating, updating, or deleting permissions through the API requires the API to modify the database schema directly.

#### List of permissions

```http
GET /[env]/permissions
```

Returns the list of permissions.

#### Single permission

```http
GET /[env]/permissions/[pk]
```

Returns the details of a single permission.

#### Create a new permission

```http
POST /[env]/permissions
```

Creates a new permission.

#### Update a permission

```http
PATCH /[env]/permissions/[pk]
```

Updates the details of a given permission.

#### Delete a permission

```http
DELETE /[env]/permissions/[pk]
```

Permanently deletes a permission.

### Relations

These endpoints are used for creating, updating, or deleting relations.

#### List of relations

```http
GET /[env]/relations
```

Returns the list of relations.

#### Single relation

```http
GET /[env]/relations/[pk]
```

Returns the details of a single relation.

#### Create a new relation

```http
POST /[env]/relations
```

Creates a new relation.

#### Update a relation

```http
PATCH /[env]/relations/[pk]
```

Updates the details of a given relation.

#### Delete a relation

```http
DELETE /[env]/relations/[pk]
```

Permanently deletes a relation.

### Roles

These endpoints are used for creating, updating, or deleting roles.

#### List of roles

```http
GET /[env]/roles
```

Returns the list of roles.

#### Single role

```http
GET /[env]/roles/[pk]
```

Returns the details of a single role.

#### Create a new role

```http
POST /[env]/roles
```

Creates a new role.

#### Update a role

```http
PATCH /[env]/roles/[pk]
```

Updates the details of a given role.

#### Delete a role

```http
DELETE /[env]/roles/[pk]
```

Permanently deletes a role.

### Settings

These endpoints are used for creating, updating, or deleting settings.

#### List of settings

```http
GET /[env]/settings
```

Returns the list of settings.

#### Single setting

```http
GET /[env]/settings/[pk]
```

Returns the details of a single setting.

#### Create a new setting

```http
POST /[env]/settings
```

Creates a new setting.

#### Update a setting

```http
PATCH /[env]/settings/[pk]
```

Updates the details of a given setting.

#### Delete a setting

```http
DELETE /[env]/setting/[pk]
```

Permanently deletes a setting.

### Collections

These endpoints are used for creating, updating, or deleting settings. Similar to `/fields`, it alters the database schema directly.

#### List of collctions

```http
GET /[env]/collections
```

Returns the list of collections.

#### Single collection

```http
GET /[env]/collections/[pk]
```

Returns the details of a single collection.

#### Create a new collection

```http
POST /[env]/collections
```

Creates a new collection.

#### Update a collection

```http
PATCH /[env]/collections/[pk]
```

Updates the details of a given collection.

#### Delete a collection

```http
DELETE /[env]/collections/[pk]
```

Permanently deletes a collection information, the table and all its contents.

### Get Revision

Get a specific revision

```http
GET /[env]/revisions/[pk]
```

#### Query Parameters

| Name   | Default   | Description                                                |
| ------ | --------- | ---------------------------------------------------------- |
|        |           | @TODO |

#### Common Responses

| Code            | Description                                                                  |
| --------------- | ---------------------------------------------------------------------------- |
| 200 OK          | `data`: A single Directus Revision<br>`meta`: Depends on requested metadata |
| 400 Bad Request | `message`: Syntax error in provided JSON                                     |

#### Examples

*   Get the revision with primary key 91
    ```bash
    curl https://api.directus.io/_/revisions/91
    ```

### Get Revisions

Get all item revisions, for all collections within this instance

```http
GET /revisions
```

#### Query Parameters

| Name   | Default   | Description                                                |
| ------ | --------- | ---------------------------------------------------------- |
|        |           | @TODO |

#### Common Responses

| Code            | Description                                                                  |
| --------------- | ---------------------------------------------------------------------------- |
| 200 OK          | `data`: Array of Directus Revisions<br>`meta`: Depends on requested metadata |
| 400 Bad Request | `message`: Syntax error in provided JSON                                     |

#### Examples

*   Get all the Directus revisions for this instance
    ```bash
    curl https://api.directus.io/_/revisions
    ```

### Create User

Creates a new user within this instance

```http
POST /users
```

#### Body

The email and password for the new user to be created. Any other submitted fields are optional, but field keys must match column names within `directus_users`.

```json
{
    "email": "rijk@directus.io",
    "password": "d1r3ctus"
}
```

#### Common Responses

| Code                     | Description                                                          |
| ------------------------ | -------------------------------------------------------------------- |
| 201 Created              | `data`: The created user, including default fields added by Directus |
| 400 Bad Request          | `message`: Syntax error in provided JSON                             |
| 422 Unprocessable Entity | `message`: Column doesn’t exist in collection                        |

### Get User

Gets a single user from within this instance

```http
GET /users/[pk]
GET /users/[pk],[pk],[pk]
```

#### Query Parameters

| Name   | Default   | Description                                                |
| ------ | --------- | ---------------------------------------------------------- |
| fields | \*        | CSV of fields to include in response [Learn More](#fields) |
| meta   |           | CSV of metadata fields to include [Learn More](#metadata)  |
| status | Published | CSV of statuses [Learn More](#status)                      |
| lang   | \*        | Include translation(s) [Learn More](#language)             |

#### Common Responses

| Code          | Description                                                     |
| ------------- | --------------------------------------------------------------- |
| 200 OK        | `data`: Retrieved user<br>`meta`: Depends on requested metadata |
| 404 Not Found | `message`: Item doesn't exist within collection                 |

#### Examples

*   Return the user with an ID of `1`
    ```bash
    curl https://api.directus.io/_/users/1
    ```

### Get Users

Gets Directus users within this instance

```http
GET /users
```

#### Query Parameters

| Name          | Default   | Description                                                |
| ------------- | --------- | ---------------------------------------------------------- |
| limit         | 200       | The number of items to request                             |
| offset        | 0         | How many items to skip before fetching results             |
| sort          | id        | CSV of fields to sort by [Learn More](#sorting)            |
| fields        | \*        | CSV of fields to include in response [Learn More](#fields) |
| filter[field] |           | Filter items using operators [Learn More](#filtering)      |
| meta          |           | CSV of metadata fields to include [Learn More](#metadata)  |
| status        | Published | CSV of statuses [Learn More](#status)                      |
| lang          | \*        | Include translation(s) [Learn More](#language)             |
| q             |           | Search string [Learn More](#search-query)                  |
| id            |           | CSV of primary keys to fetch                               |

#### Common Responses

| Code            | Description                                                              |
| --------------- | ------------------------------------------------------------------------ |
| 200 OK          | `data`: Array of Directus users<br>`meta`: Depends on requested metadata |
| 400 Bad Request | `message`: Syntax error in provided JSON                                 |

#### Examples

*   Get all the Directus users for this instance
    ```bash
    curl https://api.directus.io/_/users
    ```

### Update User

Update a user within this instance

```http
PATCH /users/[pk]
```

@TODO DO WE WANT TO SUPPORT CSV OF PKs HERE TOO?

*   **PATCH** will partially update the item with the provided data, any missing fields will be ignored

#### Body

A single user to be updated. Field keys must match column names within `directus_users`.

#### Common Responses

| Code                     | Description                                                          |
| ------------------------ | -------------------------------------------------------------------- |
| 200 OK                   | `data`: The updated item, including default fields added by Directus |
| 400 Bad Request          | `message`: Syntax error in provided JSON                             |
| 404 Not Found            | `message`: Collection doesn’t exist @TODO NO USER FOUND?             |
| 422 Unprocessable Entity | `message`: Column doesn’t exist in collection                        |

### Delete User

Deletes one or more users from this instance

```http
DELETE /users/[pk]
DELETE /users/[pk],[pk],[pk]
```

#### Common Responses

| Code           | Description                                           |
| -------------- | ----------------------------------------------------- |
| 204 No Content | User was successfully deleted                         |
| 404 Not Found  | `message`: User doesn't exist within `directus_users` |

### Invite User

Invite a new user to this instance. This will send an email to the user with further instructions

```http
POST /users/invite
```

#### Body

An email, or an array of emails to send invites to.

```json
{
    "email": "rijk@directus.io"
}
```

or

```
{
  "email": [
    "rijk@directus.io",
    "welling@directus.io",
    "ben@directus.io"
  ]
}
```

#### Common Responses

| Code                     | Description                              |
| ------------------------ | ---------------------------------------- |
| 200 OK                   | Emails successfully sent                 |
| 400 Bad Request          | `message`: Syntax error in provided JSON |
| 422 Unprocessable Entity | `message`: Email is invalid              |

### Track User

Set the time and last Directus App page accessed by the user. Last Access is used to determine if the user is still logged into the Directus app, and Last Page is used to avoid editing conflicts between multiple users.

```http
PATCH /users/[pk]/tracking/page
```

#### Body

The path to the last page the user was on in the Directus App

```json
{
    "last_page": "/tables/projects"
}
```

#### Common Responses

| Code                     | Description                              |
| ------------------------ | ---------------------------------------- |
| 200 OK                   | User successfully tracked                |
| 400 Bad Request          | `message`: Syntax error in provided JSON |
| 422 Unprocessable Entity | `message`: Field is invalid              |


#### List of Revisions

```http
GET /[env]/users/[pk]/revisions
```

Returns a list of revisions for a single user.

#### Single revision

```http
GET /[env]/users/[pk]/revisions/[offset]
```

Returns the revision of a single user using a 0-index based offset.

#### Revert detail

```http
GET /[env]/users/[pk]/revert/[revision-pk]
```

Reverts the details of a user to a given revision.

## Utilities

### Hash String

Hashes the submitted string using the chosen algorithm

```http
POST /utils/hash
```

#### Body

The hashing algorithm to use and the string to hash

```json
{
    "hasher": "core|bcrypt|sha1|sha224|sha256|sha384|sha512",
    "string": "Directus"
}
```

#### Common Responses

| Code            | Description                              |
| --------------- | ---------------------------------------- |
| 200 OK          | `data`: The hashed string                |
| 400 Bad Request | `message`: Syntax error in provided JSON |

### Match Hashed String

Confirms encrypted hashes against the API

```http
POST /utils/hash/match
```

#### Body

The hashing algorithm to use and the string to hash

```json
{
    "hasher": "core|bcrypt|sha1|sha224|sha256|sha384|sha512",
    "string": "Directus",
    "hash": "c898896f3f70f61bc3fb19bef222aa860e5ea717"
}
```

#### Common Responses

| Code            | Description                                                                              |
| --------------- | ---------------------------------------------------------------------------------------- |
| 200 OK          | `data`: Boolean. Note that `false` (string does not match hash) is a successful response |
| 400 Bad Request | `message`: Syntax error in provided JSON                                                 |

### Get Random String

Gets a random alphanumeric string from the API

```http
GET /utils/random/string
```

| Name   | Default | Description                |
| ------ | ------- | -------------------------- |
| length | 32      | Length of string to return |

#### Common Responses

| Code            | Description                              |
| --------------- | ---------------------------------------- |
| 200 OK          | `data`: The random string                |
| 400 Bad Request | `message`: Syntax error in provided JSON |


### Mail

Send a email to one or multiple emails

```http
POST /[env]/mail
```

#### Body

```json
{
  "to": [1, "user@example.com", 2, {"email": "intern@example.com", "name": "Jane Doe"}],
  "subject": "New password",
  "body": "Hello <b>{{name}}</b>, this is your new password: {{password}}.",
  "type": "html",
  "data": {
    "user": "John Doe",
    "password": "secret"
  }
}
```

## SCIM

Directus partly supports Version 2 of System for Cross-domain Identity Management, or SCIM. This open standard allows for users to be created, managed, and disabled outside of Directus so that enterprise clients have the ability to use a single, centralize system for user provisioning.

### Endpoints

| Endpoint     | Methods                 |
| ------------ | ----------------------- |
| /Users       | GET, POST               |
| /Users/{id}  | GET, PUT, PATCH         |
| /Groups      | GET, POST               |
| /Groups/{id} | GET, PUT, PATCH, DELETE |

Read more in the "SCIM Endpoints and HTTP Methods" section of  [RFC7644](https://tools.ietf.org/html/rfc7644#section-3.2).

### Get Users

```
GET /scim/v2/Users
```

#### Parameters
| Name       | Type        | Description 
| ---------- | ------------| ------------
| startIndex | `Integer`   | The 1-based index of the first result in the current set of list results.
| count      | `Integer`   | Specifies the desired maximum number of query results per page.
| filter     | `String`    | `id`, `userName`, `emails.value` and `externalId` attribute Supported. Only operator `eq` is supported.

```
GET /scim/v2/Users?filter=userName eq user@example.com
```

#### Response

```json
{
  "schemas": [
    "urn:ietf:params:scim:api:messages:2.0:ListResponse"
  ],
  "totalResults": 3,
  "Resources": [
    {
      "schemas": [
        "urn:ietf:params:scim:schemas:core:2.0:User"
      ],
      "id": "789",
      "externalId": 1,
      "meta": {
          "resourceType": "User",
          "location": "http://example.com/_/scim/v2/Users/789",
          "version": "W/\"fb2c131da3a58d1f32800c3179cdfe50\""
      },
      "name": {
          "familyName": "User",
          "givenName": "Admin"
      },
      "userName": "admin@example.com",
      "emails": [
          {
              "value": "admin@example.com",
              "type": "work",
              "primary": true
          }
      ],
      "locale": "en-US",
      "timezone": "Europe/Berlin",
      "active": true
    },
    {
      "schemas": [
        "urn:ietf:params:scim:schemas:core:2.0:User"
      ],
      "id": "345",
      "externalId": 2,
      "meta": {
        "resourceType": "User",
        "location": "http://example.com/_/scim/v2/Users/345",
        "version": "W/\"68c210ea2la8isj2ba11d8b3b2982d\""
      },
      "name": {
        "familyName": "User",
        "givenName": "Intern"
      },
      "userName": "intern@example.com",
      "emails": [
        {
          "value": "intern@example.com",
          "type": "work",
          "primary": true
        }
      ],
      "locale": "en-US",
      "timezone": "America/New_York",
      "active": true
    },
    {
      "schemas": [
        "urn:ietf:params:scim:schemas:core:2.0:User"
      ],
      "id": "123",
      "externalId": 3,
      "meta": {
        "resourceType": "User",
        "location": "http://example.com/_/scim/v2/Users/123",
        "version": "W/\"20e4fasdf0jkdf9aa497f55598c8c883\""
      },
      "name": {
        "familyName": "User",
        "givenName": "Disabled"
      },
      "userName": "disabled@example.com",
      "emails": [
        {
          "value": "disabled@example.com",
          "type": "work",
          "primary": true
        }
      ],
      "locale": "en-US",
      "timezone": "America/New_York",
      "active": false
    }
  ]
}
```

### Get User

```
GET /scim/v2/Users/:id
```

#### Response:

```json
{
  "schemas": [
    "urn:ietf:params:scim:schemas:core:2.0:User"
  ],
  "id": "789",
  "externalId": 1,
  "meta": {
    "resourceType": "User",
    "location": "http://example.com/_/scim/v2/Users/789",
    "version": "W/\"fb2c131da3a58d1f32800c3179cdfe50\""
  },
  "name": {
    "familyName": "User",
    "givenName": "Admin"
  },
  "userName": "admin@example.com",
  "emails": [
    {
      "value": "admin@example.com",
      "type": "work",
      "primary": true
    }
  ],
  "locale": "en-US",
  "timezone": "Europe/Berlin",
  "active": true
}
```

### Create User

```
POST /scim/v2/Users
```

#### Body

```json
{
     "schemas":["urn:ietf:params:scim:schemas:core:2.0:User"],
     "userName":"johndoe@example.com",
     "externalId":"johndoe-id",
     "name":{
       "familyName":"Doe",
       "givenName":"John"
     }
   }
```

#### Response

```json
{
  "schemas": [
    "urn:ietf:params:scim:schemas:core:2.0:User"
  ],
  "id": "johndoe-id",
  "externalId": 4,
  "meta": {
    "resourceType": "User",
    "location": "http://example.com/_/scim/v2/Users/johndoe-id",
    "version": "W/\"fb2c131ad3a58d1f32800c1379cdfe50\""
  },
  "name": {
    "familyName": "Doe",
    "givenName": "John"
  },
  "userName": "johndoe@example.com",
  "emails": [
    {
      "value": "johndoe@example.com",
      "type": "work",
      "primary": true
    }
  ],
  "locale": "en-US",
  "timezone": "America/New_York",
  "active": false
}
```

### Update User

```
PATCH /scim/v2/Users/:id
```

#### Body
```json
{
     "schemas":["urn:ietf:params:scim:schemas:core:2.0:User"],
     "name":{
       "familyName":"Doe",
       "givenName":"Johnathan"
     }
   }
```

#### Response

```json
{
  "schemas": [
    "urn:ietf:params:scim:schemas:core:2.0:User"
  ],
  "id": "johndoe-id",
  "externalId": 4,
  "meta": {
    "resourceType": "User",
    "location": "http://example.com/_/scim/v2/Users/johndoe-id",
    "version": "W/\"fb2c131ad3a66d1f32800c1379cdfe50\""
  },
  "name": {
    "familyName": "Doe",
    "givenName": "Johnathan"
  },
  "userName": "johndoe@example.com",
  "emails": [
    {
      "value": "johndoe@example.com",
      "type": "work",
      "primary": true
    }
  ],
  "locale": "en-US",
  "timezone": "America/New_York",
  "active": false
}
```


### Get Groups

```
GET /scim/v2/Groups
```

#### Parameters
| Name       | Type        | Description 
| ---------- | ------------| ------------
| startIndex | `Integer`   | The 1-based index of the first result in the current set of list results.
| count      | `Integer`   | Specifies the desired maximum number of query results per page.
| filter     | `String`    | `displayName` attribute Supported. Only operator `eq` is supported.

```
GET /scim/v2/Groups
```

#### Response

```json
{
  "schemas": [
    "urn:ietf:params:scim:api:messages:2.0:ListResponse"
  ],
  "totalResults": 3,
  "Resources": [
    {
      "schemas": [
        "urn:ietf:params:scim:schemas:core:2.0:Group"
      ],
      "id": "one",
      "externalId": 1,
      "meta": {
        "resourceType": "Group",
        "location": "http://example.com/_/scim/v2/Groups/one",
        "version": "W/\"7b7bc2512ee1fedcd76bdc68926d4f7b\""
      },
      "displayName": "Administrator",
      "members": [
        {
          "value": "admin@example.com",
          "$ref": "http://example.com/_/scim/v2/Users/789",
          "display": "Admin User"
        }
      ]
    },
    {
      "schemas": [
        "urn:ietf:params:scim:schemas:core:2.0:Group"
      ],
      "id": "two",
      "externalId": 2,
      "meta": {
        "resourceType": "Group",
        "location": "http://example.com/_/scim/v2/Groups/two",
        "version": "W/\"3d067bedfe2f4677470dd6ccf64d05ed\""
      },
      "displayName": "Public",
      "members": []
    },
    {
      "schemas": [
        "urn:ietf:params:scim:schemas:core:2.0:Group"
      ],
      "id": "three",
      "externalId": 3,
      "meta": {
        "resourceType": "Group",
        "location": "http://example.com/_/scim/v2/Groups/three",
        "version": "W/\"17ac93e56edd16cafa7b57979b959292\""
      },
      "displayName": "Intern",
      "members": [
        {
            "value": "intern@example.com",
            "$ref": "http://example.com/_/scim/v2/Users/345",
            "display": "Intern User"
        },
        {
            "value": "disabled@example.com",
            "$ref": "http://example.com/_/scim/v2/Users/123",
            "display": "Disabled User"
        }
      ]
    }
  ]
}
```

### Get Group

```
GET /scim/v2/Groups/:id
```

#### Response:

```json
{
  "schemas": [
    "urn:ietf:params:scim:schemas:core:2.0:Group"
  ],
  "id": "one",
  "externalId": 1,
  "meta": {
    "resourceType": "Group",
    "location": "http://example.com/_/scim/v2/Groups/one",
    "version": "W/\"7b7bc2512ee1fedcd76bdc68926d4f7b\""
  },
  "displayName": "Administrator",
  "members": [
    {
      "value": "admin@example.com",
      "$ref": "http://example.com/_/scim/v2/Users/1",
      "display": "Admin User"
    }
  ]
}
```

### Create Group

```
POST /scim/v2/Users
```

#### Body

```json
{
  "schemas":["urn:ietf:params:scim:schemas:core:2.0:Group"],
  "displayName":"Editors",
  "externalId":"editors-id"
}
```

#### Response

```json
{
  "schemas": [
    "urn:ietf:params:scim:schemas:core:2.0:Group"
  ],
  "id": "editors-id",
  "externalId": 4,
  "meta": {
    "resourceType": "Group",
    "location": "http://example.com/_/scim/v2/Groups/editors-id",
    "version": "W/\"7b7bc2512ee1fedcd76bdc68926d4f7b\""
  },
  "displayName": "Editors",
  "members": []
}
```

### Update Group

```
PATCH /scim/v2/Groups/:id
```

#### Body
```json
{
  "schemas":["urn:ietf:params:scim:schemas:core:2.0:Group"],
  "displayName":"Writers"
}
```

#### Response

```json
{
  "schemas": [
    "urn:ietf:params:scim:schemas:core:2.0:Group"
  ],
  "id": "editors-id",
  "externalId": 4,
  "meta": {
    "resourceType": "Group",
    "location": "http://example.com/_/scim/v2/Groups/editors-id",
    "version": "W/\"7b7bc2512ee1fedcd76bdc68926d4f7b\""
  },
  "displayName": "Writers",
  "members": []
}
```

### Delete Group

```
DELETE /scim/v2/Groups/:id
```

#### Response

Empty response when successful.

## Extend API endpoints

### Interfaces

All endpoints defined in a interface will be located under the `interfaces` group.

```http
/[env]/interfaces/[interface-id]
```

### Pages

All endpoints defined in a page will be located under the `pages` group.

```http
/[env]/pages/[interface-id]
```

### Custom endpoints 

All endpoints created by the user, that it's not related to any extension will be located under the `custom` group endpoints.

```http
`/[env]/custom/[endpoint-id]`
```

These endpoints are used for creating, updating, or deleting settings. Similar to `/fields`, it alters the database schema directly.

## Extensions

Directus can easily be extended through the addition of several types of extensions. Extensions are important pieces of the Directus App that live in the decoupled Directus API. These include Interfaces, Listing Views, and Pages. These three different types of extensions live in their own directory and may have their own endpoints.

### Get Interfaces, List Views, Pages

These endpoints read the API's file system for directory names and return an array of extension names as well as the contents of each's `meta.json` files.

```http
GET /interfaces
GET /listings
GET /pages
```

#### Common Responses

| Code   | Description                         |
| ------ | ----------------------------------- |
| 200 OK | `data`: An array of extension names |

<!--
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
-->

## API Information endpoints 

### Server

Returns information about the server and api.

```http
GET /
```

### Ping the server

```http
GET /server/ping
```

### Data Types

Returns the list of Directus data types.

```http
GET /types
```

## Webhooks

Webhooks allows you to send a HTTP request when an event happens.

Creating a webhook on Directus is done by creating a custom hook that makes a HTTP request.

Below there's an example that sends a `POST` request to `http://example.com/alert` every time an article is created, using the following payload:

```json
{
  "type": "article",
  "data": {
    "title": "new article",
    "body": "this is a new article"
  }
}
```

```php
<?php

return [
    'actions' => [
        // Send an alert when a post is created
        'collection.insert.articles' => function (array $data) {
            $client = new \GuzzleHttp\Client([
                'base_uri' => 'http://example.com'
            ]);

            $data = [
                'type' => 'article',
                'data' => $data
            ];

            $response = $client->request('POST', 'alert', [
                'json' => $data
            ]);
        }
    ]
];
```
