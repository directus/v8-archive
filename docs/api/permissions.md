---
pageClass: page-reference
---

# Permissions

<two-up>

::: slot left
Permissions control who has access to what and when.
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

---

## The Permissions Object

<two-up>
<template slot="left">
<def-list>

#### id <def-type>integer</def-type>
Unique identifier for the permission.

#### collection <def-type>string</def-type>
What collection this permission applies to.

#### role <def-type>integer</def-type>
Unique identifier of the role this permission applies to.

#### status <def-type>string</def-type>
What status this permission applies to.

#### create <def-type>string</def-type>
If the user can create items. One of `none`, `full`.

#### read <def-type>string</def-type>
If the user can read items. One of `none`, `mine`, `role`, `full`.

#### update <def-type>string</def-type>
If the user can update items. One of `none`, `mine`, `role`, `full`.

#### delete <def-type>string</def-type>
If the user can update items. One of `none`, `mine`, `role`, `full`.

#### comment <def-type>string</def-type>
If the user can post comments. One of `none`, `create`, `update`, `full`.

#### explain <def-type>string</def-type>
If the user is required to leave a comment explaining what was changed. One of `none`, `create`, `update`, `always`.

#### read_field_blacklist <def-type>array</def-type>
Explicitly denies read access for specific fields.

#### write_field_blacklist <def-type>array</def-type>
Explicitly denies write access for specific fields.

#### status_blacklist <def-type>array</def-type>
Explicitly denies specific statuses to be used.

</def-list>
</template>

<info-box title="Permission Object" slot="right" class="sticky">

```json
{
  "id": 1,
  "collection": "customers",
  "role": 3,
  "status": null,
  "create": "full",
  "read": "mine",
  "update": "none",
  "delete": "none",
  "comment": "update",
  "explain": "none",
  "read_field_blacklist": [],
  "write_field_blacklist": [],
  "status_blacklist": []
}
```

</info-box>
</two-up>

---

## List the Permissions

<two-up>
<template slot="left">

List all permissions.

### Parameters

<def-list>

!!! include params/project.md !!!

</def-list>

### Query

<def-list>

!!! include query/fields.md !!!
!!! include query/limit.md !!!
!!! include query/offset.md !!!
!!! include query/page.md !!!
!!! include query/sort.md !!!
!!! include query/single.md !!!
!!! include query/filter.md !!!
!!! include query/q.md !!!
!!! include query/meta.md !!!

</def-list>

### Returns

Returns an array of [permission objects](#the-permissions-object).

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /:project/permissions
```

</info-box>
<info-box title="Response">

```json
{
  "data": [
    {
      "id": 1,
      "collection": "customers",
      "role": 3,
      "status": null,
      "create": "full",
      "read": "mine",
      "update": "none",
      "delete": "none",
      "comment": "update",
      "explain": "none",
      "read_field_blacklist": [],
      "write_field_blacklist": [],
      "status_blacklist": []
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

## Retrieve a Permission

<two-up>
<template slot="left">

Retrieve a single permissions object by unique identifier.

### Paremeters

<def-list>

!!! include params/project.md !!!
!!! include params/id.md !!!

</def-list>

### Query

<def-list>

!!! include query/fields.md !!!
!!! include query/meta.md !!!

</def-list>

### Returns

Returns the [permissions object](#the-permissions-object) for the given unique identifier.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /:project/permissions/:id
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "id": 1,
    "collection": "customers",
    "role": 3,
    "status": null,
    "create": "full",
    "read": "full",
    "update": "mine",
    "delete": "mine",
    "comment": "none",
    "explain": "none",
    "read_field_blacklist": [],
    "write_field_blacklist": [],
    "status_blacklist": []
  }
}
```

</info-box>
</div>
</template>
</two-up>

---

## List the Current User's Permissions

<two-up>
<template slot="left">

List the permissions that apply to the current user.

::: tip
This endpoint won't work for the public role.
:::

### Parameters

<def-list>

!!! include params/project.md !!!

</def-list>

### Query

No query parameters available.

### Returns

Returns an array of [permission objects](#the-permissions-object).

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /:project/permissions/me
```

</info-box>
<info-box title="Response">

```json
{
  "data": [
    {
      "id": 1,
      "collection": "customers",
      "role": 3,
      "status": null,
      "create": "full",
      "read": "mine",
      "update": "none",
      "delete": "none",
      "comment": "update",
      "explain": "none",
      "read_field_blacklist": [],
      "write_field_blacklist": [],
      "status_blacklist": []
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

## List the Current User's Permissions for Given Collection

<two-up>
<template slot="left">

List the permissions that apply to the current user for the given collection

::: tip
This endpoint won't work for the public role.
:::

### Parameters

<def-list>

!!! include params/project.md !!!

#### collection <def-type alert>required</def-type>
Collection of which you want to retrieve the permissions.

</def-list>

### Query

No query parameters available.

### Returns

Returns a [permissions object](#the-permissions-object) if available.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /:project/permissions/me/:collection
```

</info-box>
<info-box title="Response">

```json
{
  "data": {
    "id": 1,
    "collection": "customers",
    "role": 3,
    "status": null,
    "create": "full",
    "read": "mine",
    "update": "none",
    "delete": "none",
    "comment": "update",
    "explain": "none",
    "read_field_blacklist": [],
    "write_field_blacklist": [],
    "status_blacklist": []
  }
}
```

</info-box>
</div>
</template>
</two-up>

---

## Create a Permission

<two-up>
<template slot="left">

Create a new permission.

### Parameters

<def-list>

!!! include params/project.md !!!

</def-list>

### Attributes

<def-list>

#### collection <def-type alert>Required</def-type>
What collection this permission applies to.

#### role <def-type alert>Required</def-type>
Unique identifier of the role this permission applies to.

#### status <def-type>optional</def-type>
What status this permission applies to.

#### create <def-type>optional</def-type>
If the user can create items. One of `none`, `full`.

#### read <def-type>optional</def-type>
If the user can read items. One of `none`, `mine`, `role`, `full`.

#### update <def-type>optional</def-type>
If the user can update items. One of `none`, `mine`, `role`, `full`.

#### delete <def-type>optional</def-type>
If the user can update items. One of `none`, `mine`, `role`, `full`.

#### comment <def-type>optional</def-type>
If the user can post comments. One of `none`, `create`, `update`, `full`.

#### explain <def-type>optional</def-type>
If the user is required to leave a comment explaining what was changed. One of `none`, `create`, `update`, `always`.

#### read_field_blacklist <def-type>optional</def-type>
Explicitly denies read access for specific fields.

#### write_field_blacklist <def-type>optional</def-type>
Explicitly denies write access for specific fields.

#### status_blacklist <def-type>optional</def-type>
Explicitly denies specific statuses to be used.

</def-list>

### Query

<def-list>

!!! include query/meta.md !!!

</def-list>

### Returns

Returns the [permissions object](#the-permissions-object) for the permission that was just created.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
  POST /:project/permissions
```

</info-box>

<info-box title="Request">

```json
{
  "collection": "customers",
  "role": 3,
  "read": "mine",
  "read_field_blacklist": ["featured_image"]
}
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "id": 28,
    "collection": "customers",
    "role": 3,
    "status": null,
    "create": "none",
    "read": "mine",
    "update": "none",
    "delete": "none",
    "comment": "none",
    "explain": "none",
    "read_field_blacklist": [
      "featured_image"
    ],
    "write_field_blacklist": [],
    "status_blacklist": []
  }
}
```

</info-box>
</div>
</template>
</two-up>

---

## Update a Permission

<two-up>
<template slot="left">

Update an existing permission

### Parameters

<def-list>

!!! include params/project.md !!!
!!! include params/id.md !!!

</def-list>

### Attributes

<def-list>

#### collection <def-type>optional</def-type>
What collection this permission applies to.

#### role <def-type>optional</def-type>
Unique identifier of the role this permission applies to.

#### status <def-type>optional</def-type>
What status this permission applies to.

#### create <def-type>optional</def-type>
If the user can create items. One of `none`, `full`.

#### read <def-type>optional</def-type>
If the user can read items. One of `none`, `mine`, `role`, `full`.

#### update <def-type>optional</def-type>
If the user can update items. One of `none`, `mine`, `role`, `full`.

#### delete <def-type>optional</def-type>
If the user can update items. One of `none`, `mine`, `role`, `full`.

#### comment <def-type>optional</def-type>
If the user can post comments. One of `none`, `create`, `update`, `full`.

#### explain <def-type>optional</def-type>
If the user is required to leave a comment explaining what was changed. One of `none`, `create`, `update`, `always`.

#### read_field_blacklist <def-type>optional</def-type>
Explicitly denies read access for specific fields.

#### write_field_blacklist <def-type>optional</def-type>
Explicitly denies write access for specific fields.

#### status_blacklist <def-type>optional</def-type>
Explicitly denies specific statuses to be used.

</def-list>

### Query

<def-list>

!!! include query/meta.md !!!

</def-list>

### Returns

Returns the [permissions object](#the-permissions-object) for the permission that was just updated.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
 PATCH /:project/permissions/:id
```

</info-box>

<info-box title="Request">

```json
{
  "read": "full"
}
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "id": 28,
    "collection": "customers",
    "role": 3,
    "status": null,
    "create": "none",
    "read": "full",
    "update": "none",
    "delete": "none",
    "comment": "none",
    "explain": "none",
    "read_field_blacklist": [
      "featured_image"
    ],
    "write_field_blacklist": [],
    "status_blacklist": []
  }
}
```

</info-box>
</div>
</template>
</two-up>

---

## Delete a Permission

<two-up>
<template slot="left">

Delete an existing permission

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
DELETE /:project/permissions/:id
```

</info-box>
</div>
</template>
</two-up>

---