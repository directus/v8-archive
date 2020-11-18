---
pageClass: page-reference
---

# SCIM

<two-up>

::: slot left
Directus partially supports Version 2 of System for Cross-domain Identity Management (SCIM). It is an open standard that allows for the exchange of user information between systems, therefore allowing users to be externally managed using the endpoints described below.
:::

<info-box title="Endpoints" slot="right">

```endpoints
   GET /:project/scim/v2/Users
   GET /:project/scim/v2/Users/:id
  POST /:project/scim/v2/Users
 PATCH /:project/scim/v2/Users/:id
DELETE /:project/scim/v2/Users/:id
   GET /:project/scim/v2/Groups
   GET /:project/scim/v2/Groups/:id
  POST /:project/scim/v2/Groups/:id
 PATCH /:project/scim/v2/Groups/:id
DELETE /:project/scim/v2/Groups/:id
```

</info-box>
</two-up>

---

## List SCIM Users

<two-up>
<template slot="left">

List the SCIM users

### Parameters

<def-list>

!!! include params/project.md !!!

</def-list>

### Query

<def-list>

#### startIndex <def-type>optional</def-type>
The 1-based index of the first result in the current set of list results.

#### count <def-type>optional</def-type>
Specifies the desired maximum number of query results per page.

#### filter <def-type>optional</def-type>
Filter by `id`, `userName`, `emails.value` and `externalId` attributes are supported. Only the `eq` operator is supported. Uses format `?filter=id eq 15`

</def-list>

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
GET /:project/scim/v2/Users
```

</info-box>
<info-box title="Response">

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
    { ... },
    { ... }
  ]
}
```

</info-box>
</div>
</template>
</two-up>

---

## Retrieve a SCIM User

<two-up>
<template slot="left">

Retrieve a single SCIM user by unique identifier.

### Parameters

<def-list>

!!! include params/project.md !!!

#### external_id <def-type alert>required</def-type>
The `external_id` saved in `directus_users`. Corresponds to the `id` in the SCIM Users endpoint result.

</def-list>

### Query

No query parameters available.

### Returns

Returns the SCIM User for the given unique identifier.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /:project/scim/v2/Users/:external_id
```

</info-box>

<info-box title="Response">

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

</info-box>
</div>
</template>
</two-up>

---

## Create a SCIM User

<two-up>
<template slot="left">

Create a new SCIM User.

### Parameters

<def-list>

!!! include params/project.md !!!

</def-list>

### Attributes

See [the SCIM Specification](http://www.simplecloud.info/#Specification) for more information.

### Query

No query parameters available.

### Returns

Returns the SCIM User for the user that was just created.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
  POST /:project/scim/v2/Users
```

</info-box>

<info-box title="Request">

```json
{
  "schemas": [
    "urn:ietf:params:scim:schemas:core:2.0:User"
  ],
  "userName": "johndoe@example.com",
  "externalId": "johndoe-id",
  "name": {
    "familyName": "Doe",
    "givenName": "John"
  }
}
```

</info-box>

<info-box title="Response">

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

</info-box>
</div>
</template>
</two-up>

---

## Update a SCIM User

<two-up>
<template slot="left">

Update an existing SCIM User

### Parameters

<def-list>

!!! include params/project.md !!!

#### external_id <def-type alert>required</def-type>
The `external_id` saved in `directus_users`. Corresponds to the `id` in the SCIM Users endpoint result.

</def-list>

### Attributes

See [the SCIM Specification](http://www.simplecloud.info/#Specification) for more information.

### Query

No query parameters available.

### Returns

Returns the SCIM User for the user that was just created.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
 PATCH /:project/scim/v2/Users/:external_id
```

</info-box>

<info-box title="Request">

```json
{
  "schemas": [
    "urn:ietf:params:scim:schemas:core:2.0:User"
  ],
  "name": {
    "familyName": "Doe",
    "givenName": "Johnathan"
  }
}
```

</info-box>

<info-box title="Response">

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

</info-box>
</div>
</template>
</two-up>

---

## Delete a SCIM User

<two-up>
<template slot="left">

Delete an existing SCIM User

### Parameters

<def-list>

!!! include params/project.md !!!

#### external_id <def-type alert>required</def-type>
The `external_id` saved in `directus_users`. Corresponds to the `id` in the SCIM Users endpoint result.

</def-list>

### Returns

Returns an empty body with HTTP status 204

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
DELETE /:project/scim/v2/Users/:id
```

</info-box>
</div>
</template>
</two-up>

---

## List the SCIM Groups

<two-up>
<template slot="left">

List the SCIM Groups.

### Parameters

<def-list>

!!! include params/project.md !!!

</def-list>

### Query

<def-list>

#### startIndex <def-type>optional</def-type>
The 1-based index of the first result in the current set of list results.

#### count <def-type>optional</def-type>
Specifies the desired maximum number of query results per page.

#### filter <def-type>optional</def-type>
Filter by `id`, `userName`, `emails.value` and `externalId` attributes are supported. Only the `eq` operator is supported. Uses format `?filter=id eq 15`

</def-list>

### Returns

Returns an array of SCIM Groups.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /:project/scim/v2/Groups
```

</info-box>
<info-box title="Response">

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
    { ... },
    { ... }
  ]
}
```

</info-box>
</div>
</template>
</two-up>

---

## Retrieve a SCIM Group

<two-up>
<template slot="left">

Retrieve a single SCIM Group by unique identifier.

### Parameters

<def-list>

!!! include params/project.md !!!
!!! include params/id.md !!!

</def-list>

### Query

No query parameters available.

### Returns

Returns the SCIM Group for the given unique identifier.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /:project/scim/v2/Groups/:id
```

</info-box>

<info-box title="Response">

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

</info-box>
</div>
</template>
</two-up>

---

## Create a SCIM Group

<two-up>
<template slot="left">

Create a new SCIM Group.

### Parameters

<def-list>

!!! include params/project.md !!!

</def-list>

### Attributes

See [the SCIM Specification](http://www.simplecloud.info/#Specification) for more information.

### Query

No query parameters available.

### Returns

Returns the SCIM Group for the SCIM Group that was just created.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
  POST /:project/scim/v2/Groups/:id
```

</info-box>

<info-box title="Request">

```json
{
  "schemas": [
    "urn:ietf:params:scim:schemas:core:2.0:Group"
  ],
  "displayName": "Editors",
  "externalId": "editors-id"
}
```

</info-box>

<info-box title="Response">

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

</info-box>
</div>
</template>
</two-up>

---

## Update a SCIM Group

<two-up>
<template slot="left">

Update an existing SCIM Group

### Parameters

<def-list>

!!! include params/project.md !!!
!!! include params/id.md !!!

</def-list>

### Attributes

See [the SCIM Specification](http://www.simplecloud.info/#Specification) for more information.

### Query

No query parameters available.

### Returns

Returns the SCIM Group for the SCIM Group that was just updated.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
 PATCH /:project/scim/v2/Groups/:id
```

</info-box>

<info-box title="Request">

```json
{
  "schemas": [
    "urn:ietf:params:scim:schemas:core:2.0:Group"
  ],
  "displayName": "Writers"
}
```

</info-box>

<info-box title="Response">

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

</info-box>
</div>
</template>
</two-up>

---

## Delete a SCIM Group

<two-up>
<template slot="left">

Delete an existing SCIM Group

### Parameters

<def-list>

!!! include params/project.md !!!
!!! include params/id.md !!!

</def-list>

### Returns

Returns an empty body with HTTP status 204

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
DELETE /:project/scim/v2/Groups/:id
```

</info-box>
</div>
</template>
</two-up>

---