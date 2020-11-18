---
pageClass: page-reference
---

# Collections

<two-up>

::: slot left
Collections are the individual collections of items, similar to tables in a database.

Changes to collections will alter the schema of the database.
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

---

## The Collection Object

<two-up>

::: slot left
### Attributes

<def-list>

#### collection <def-type>string</def-type>

Unique name of the collection.

#### note <def-type>string</def-type>

A note describing the collection.

#### hidden <def-type>boolean</def-type>

Whether or not the collection is hidden from the navigation in the admin app.

#### single <def-type>boolean</def-type>

Whether or not the collection is treated as a single record.

#### managed <def-type>boolean</def-type>

If Directus is tracking and managing this collection currently.

#### fields <def-type>string</def-type>

The fields contained in this collection. See the [`fields`](/api/fields.html) reference for more information.

#### icon <def-type>string</def-type>

Name of a Google Material Design Icon that's assigned to this collection.

#### translation <def-type>object</def-type>

Key value pairs of how to show this collection's name in different languages in the admin app.

</def-list>

:::

<info-box title="Collection object" slot="right" class="sticky">

```json
{
  "collection": "customers",
  "note": "Our most valued money makers",
  "hidden": false,
  "single": false,
  "managed": true,
  "fields": { ... },
  "icon": "perm_contact_calendar",
  "translation": {
    "en-US": "Customers",
    "nl-NL": "Klanten"
  }
}
```

</info-box>
</two-up>

---

## List Collections

<two-up>

::: slot left
Returns a list of the collections available in the project.

### Parameters

<def-list>

!!! include params/project.md !!!

</def-list>

### Query

<def-list>

!!! include query/offset.md !!!
!!! include query/single.md !!!
!!! include query/meta.md !!!

</def-list>

### Returns

An object with a `data` property that contains an array of available [collection objects](#the-collection-object). This array also contains the Directus system collections and is never empty.
:::

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
GET /:project/collections
```

</info-box>

<info-box title="Response">

```json
{
  "data": [
  	{
      "collection": "customers",
      "note": "Our most valued money makers",
      "hidden": false,
      "single": false,
      "managed": true,
      "fields": { ... },
      "icon": "perm_contact_calendar",
      "translation": {
        "en-US": "Customers",
        "nl-NL": "Klanten"
      }
    },
    { ... },
    { ... }
  ]
}
```
</info-box>
</div>
</template>
:::

</two-up>

---

## Retrieve a Collection

<two-up>

::: slot left
Retrieves the details of a single collection.

### Parameters

<def-list>

!!! include params/project.md !!!

#### collection <def-type alert>required</def-type>
The unique name of the collection.

</def-list>

### Query

<def-list>

!!! include query/meta.md !!!

</def-list>

### Returns

Returns a [collection object](#the-collection-object).
:::

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
GET /:project/collections/:collection
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "collection": "customers",
    "note": "Our most valued money makers",
    "hidden": false,
    "single": false,
    "managed": true,
    "fields": { ... },
    "icon": "perm_contact_calendar",
    "translation": {
      "en-US": "Customers",
      "nl-NL": "Klanten"
    }
  }
}
```
</info-box>
</div>
</template>
:::

</two-up>

---

## Create a Collection

<two-up>
<template slot="left">

Create a new collection in Directus.

### Parameters

<def-list>

!!! include params/project.md !!!

</def-list>

### Attributes

<def-list>

#### collection <def-type alert>Required</def-type>

Unique name of the collection.

#### fields <def-type alert>Required</def-type>

The fields contained in this collection. See the [`fields`](/api/fields.html) reference for more information.

Each individual field requires `field`, `type`, and `interface` to be provided.

::: warning
Don't forget to create a primary key field in your collection. Without it, Directus won't be able to work correctly.
:::

#### note <def-type>optional</def-type>

A note describing the collection.

#### hidden <def-type>optional</def-type>

Whether or not the collection is hidden from the navigation in the admin app.

#### single <def-type>optional</def-type>

Whether or not the collection is treated as a single record.

#### managed <def-type>optional</def-type>

If Directus is tracking and managing this collection currently.

#### icon <def-type>optional</def-type>

Name of a Google Material Design Icon that's assigned to this collection.

#### translation <def-type>optional</def-type>

Key value pairs of how to show this collection's name in different languages in the admin app.

</def-list>

### Query

<def-list>

!!! include query/meta.md !!!

</def-list>

### Returns

Returns the newly created [collection object](#the-collection-object).

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
  POST /:project/collections
```

</info-box>

<info-box title="Request body">

```json
{
  "collection": "my_collection",
  "fields": [
    {
      "field": "id",
      "type": "integer",
      "datatype": "int",
      "length": 11,
      "interface": "numeric",
      "primary_key": true
    }
  ]
}
```
</info-box>

<info-box title="Response">

```json
{
  "data": {
    "collection": "my_collection",
    "managed": true,
    "hidden": false,
    "single": false,
    "icon": null,
    "note": null,
    "translation": null
  }
}
```
</info-box>
</div>
</template>
:::

</two-up>

---

## Update a Collection

<two-up>
<template slot="left">

Update an existing collection.

::: warning
You can't update a collection's name.
:::

### Parameters

<def-list>

!!! include params/project.md !!!

#### collection <def-type alert>required</def-type>
The collection you want to update.

</def-list>

### Attributes

<def-list>

#### note <def-type>optional</def-type>

A note describing the collection.

#### hidden <def-type>optional</def-type>

Whether or not the collection is hidden from the navigation in the admin app.

#### single <def-type>optional</def-type>

Whether or not the collection is treated as a single record.

#### managed <def-type>optional</def-type>

If Directus is tracking and managing this collection currently.

#### icon <def-type>optional</def-type>

Name of a Google Material Design Icon that's assigned to this collection.

#### translation <def-type>optional</def-type>

Key value pairs of how to show this collection's name in different languages in the admin app.

</def-list>

### Query

<def-list>

!!! include query/meta.md !!!

</def-list>

### Returns

Returns the [collection object](#the-collection-object) for the updated collection.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
 PATCH /:project/collections/:collection
```

</info-box>

<info-box title="Request body">

```json
{
  "note": "This is my first collection"
}
```
</info-box>

<info-box title="Response">

```json
{
  "data": {
    "collection": "my_collection",
    "managed": true,
    "hidden": false,
    "single": false,
    "icon": null,
    "note": "This is my first collection",
    "translation": null
  }
}
```

</info-box>
</div>
</template>
</two-up>

---

## Delete a Collection

<two-up>
<template slot="left">

Delete an existing collection.

::: danger
This will delete the whole collection, including the items within. Proceed with caution.
:::

### Parameters

<def-list>

!!! include params/project.md !!!

#### collection <def-type alert>required</def-type>
The collection you want to delete.

</def-list>

### Returns

Returns an empty body with HTTP status 204

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
DELETE /:project/collections/:collection
```

</info-box>
</div>
</template>
</two-up>

---