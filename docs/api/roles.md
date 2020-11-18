---
pageClass: page-reference
---

# Roles

<two-up>

::: slot left
Roles are groups of users that share permissions.
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

---

## The Role Object

### Attributes

<two-up>
<template slot="left">
<def-list>

#### id <def-type>integer</def-type>
Unique identifier for the role.

#### name <def-type>string</def-type>
Name of the role.

#### description <def-type>string</def-type>
Description of the role.

#### ip_whitelist <def-type>array of strings</def-type>
Array of IP addresses that are allowed to connect to the API as a user of this role.

#### external_id <def-type>string</def-type>
ID used with external services in SCIM.

#### module_listing <def-type>object</def-type>
Custom override for the admin app module bar navigation.

#### collection_listing <def-type>object</def-type>
Custom override for the admin app collection navigation.

#### enforce_2fa <def-type>boolean</def-type>
Whether or not this role enforces the use of 2FA.

</def-list>
</template>

<info-box title="Role Object" slot="right" class="sticky">

```json
{
  "id": 1,
  "name": "Administrator",
  "description": "Admins have access to all managed data within the system by default",
  "ip_whitelist": [],
  "external_id": null,
  "module_listing": null,
  "collection_listing": null,
  "enforce_2fa": false
}
```

</info-box>
</two-up>

---

## List the Roles

<two-up>
<template slot="left">

List the roles.

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

Returns an array of [role objects](#the-role-object).

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /:project/roles
```

</info-box>
<info-box title="Response">

```json
{
  "data": [
    {
      "id": 1,
      "name": "Administrator",
      "description": "Admins have access to all managed data within the system by default",
      "ip_whitelist": [],
      "external_id": null,
      "module_listing": null,
      "collection_listing": null,
      "enforce_2fa": false
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

## Retrieve a Role

<two-up>
<template slot="left">

Retrieve a single role by unique identifier.

### Parameters

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

Returns the [role object](#the-role-object) for the given unique identifier.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /:project/roles/:id
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "id": 1,
    "name": "Administrator",
    "description": "Admins have access to all managed data within the system by default",
    "ip_whitelist": [],
    "external_id": null,
    "module_listing": null,
    "collection_listing": null,
    "enforce_2fa": false
  }
}
```

</info-box>
</div>
</template>
</two-up>

---

## Create a Role

<two-up>
<template slot="left">

Create a new role.

### Parameters

<def-list>

!!! include params/project.md !!!

</def-list>

### Attributes

<def-list>

#### name <def-type alert>required</def-type>
Name of the role.

#### description <def-type>optional</def-type>
Description of the role.

#### ip_whitelist <def-type>optional of strings</def-type>
Array of IP addresses that are allowed to connect to the API as a user of this role.

#### external_id <def-type>optional</def-type>
ID used with external services in SCIM.

#### module_listing <def-type>optional</def-type>
Custom override for the admin app module bar navigation.

#### collection_listing <def-type>optional</def-type>
Custom override for the admin app collection navigation.

#### enforce_2fa <def-type>optional</def-type>
Whether or not this role enforces the use of 2FA.

</def-list>

### Query

<def-list>

!!! include query/fields.md !!!
!!! include query/meta.md !!!

</def-list>

### Returns

Returns the [role object](#the-role-object) for the role that was just created.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
  POST /:project/roles
```

</info-box>

<info-box title="Request">

```json
{
  "name": "Interns"
}
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "id": 3,
    "name": "Interns",
    "description": null,
    "ip_whitelist": [],
    "external_id": "bcb3e393-624b-4248-9b07-5bd47d2c133f",
    "module_listing": null,
    "collection_listing": null,
    "enforce_2fa": false
  }
}
```

</info-box>
</div>
</template>
</two-up>

---

## Update a Role

<two-up>
<template slot="left">

Update an existing role

### Parameters

<def-list>

!!! include params/project.md !!!
!!! include params/id.md !!!

</def-list>

### Attributes

<def-list>

#### name <def-type>optional</def-type>
Name of the role.

#### description <def-type>optional</def-type>
Description of the role.

#### ip_whitelist <def-type>optional of strings</def-type>
Array of IP addresses that are allowed to connect to the API as a user of this role.

#### external_id <def-type>optional</def-type>
ID used with external services in SCIM.

#### module_listing <def-type>optional</def-type>
Custom override for the admin app module bar navigation.

#### collection_listing <def-type>optional</def-type>
Custom override for the admin app collection navigation.

#### enforce_2fa <def-type>optional</def-type>
Whether or not this role enforces the use of 2FA.

</def-list>

### Query

<def-list>

!!! include query/fields.md !!!
!!! include query/meta.md !!!

</def-list>

### Returns

Returns the [role object](#the-role-object) for the role that was just updated.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
 PATCH /:project/roles/:id
```

</info-box>

<info-box title="Request">

```json
{
  "description": "Limited access only."
}
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "id": 3,
    "name": "Interns",
    "description": "Limited access only.",
    "ip_whitelist": [],
    "external_id": "bcb3e393-624b-4248-9b07-5bd47d2c133f",
    "module_listing": null,
    "collection_listing": null,
    "enforce_2fa": false
  }
}
```

</info-box>
</div>
</template>
</two-up>

---

## Delete a Role

<two-up>
<template slot="left">

Delete an existing role

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
DELETE /:project/roles/:id
```

</info-box>
</div>
</template>
</two-up>

---