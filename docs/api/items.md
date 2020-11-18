---
pageClass: page-reference
---

# Items

<two-up>

::: slot left
Items are individual pieces of data in your database. They can be anything, from articles, to IoT status checks.
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

---

## The Item Object

<two-up>
<template slot="left">

Items don't have a pre-defined schema. The format depends completely on how you configured your collections and fields in Directus. For the sake of documentation, we'll use a fictional `articles` collection with the following fields: `id`, `status`, `title`, `body`, `featured_image`, and `author`.

</template>

<info-box title="Example Item Object" slot="right" class="sticky">

```json
{
  "id": 1,
  "status": "published",
  "title": "Hello, world!",
  "body": "This is my first article",
  "featured_image": 2,
  "author": 5
}
```

</info-box>
</two-up>

---

## List the Items

<two-up>
<template slot="left">

List the items.

### Parameters

<def-list>

!!! include params/project.md !!!
!!! include params/collection.md !!!

</def-list>

### Query

<def-list>

!!! include query/fields.md !!!
!!! include query/limit.md !!!
!!! include query/offset.md !!!
!!! include query/sort.md !!!
!!! include query/single.md !!!
!!! include query/status.md !!!
!!! include query/filter.md !!!
!!! include query/q.md !!!
!!! include query/meta.md !!!

</def-list>

### Returns

Returns an array of [item objects](#the-item-object).

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /:project/items/:collection
```

</info-box>
<info-box title="Response">

```json
{
  "data": [
    {
      "id": 1,
      "status": "published",
      "title": "Hello, world!",
      "body": "This is my first article",
      "featured_image": 2,
      "author": 5
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

## Retrieve an Item

<two-up>
<template slot="left">

Retrieve a single item by unique identifier.

### Parameters

<def-list>

!!! include params/project.md !!!
!!! include params/collection.md !!!
!!! include params/id.md !!!

</def-list>

### Query

<def-list>

!!! include query/fields.md !!!
!!! include query/meta.md !!!

</def-list>

### Returns

Returns the [item object](#the-item-object) for the given unique identifier.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /:project/items/:collection/:id
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "id": 1,
    "status": "published",
    "title": "Hello, world!",
    "body": "This is my first article",
    "featured_image": 2,
    "author": 5
  }
}
```

</info-box>
</div>
</template>
</two-up>

---

## Create an Item

<two-up>
<template slot="left">

Create a new item.

### Parameters

<def-list>

!!! include params/project.md !!!
!!! include params/collection.md !!!

</def-list>

### Attributes

Based on your specific setup.

### Query

<def-list>

!!! include query/meta.md !!!

</def-list>

### Returns

Returns the [item object](#the-item-object) for the item that was just created.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
  POST /:project/items/:collection
```

</info-box>

<info-box title="Request">

```json
{
  "status": "published",
  "title": "Hello, world!",
  "body": "This is my first article",
  "featured_image": 2,
  "author": 5
}
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "id": 14,
    "status": "published",
    "title": "Hello, world!",
    "body": "This is my first article",
    "featured_image": 2,
    "author": 5
  }
}
```

</info-box>
</div>
</template>
</two-up>

---

## Update an Item

<two-up>
<template slot="left">

Update an existing item.

### Parameters

<def-list>

!!! include params/project.md !!!
!!! include params/collection.md !!!
!!! include params/id.md !!!

</def-list>

### Attributes

Based on your specific setup.

### Query

<def-list>

!!! include query/fields.md !!!
!!! include query/meta.md !!!

</def-list>

### Returns

Returns the [item object](#the-item-object) for the item that was just updated.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
 PATCH /:project/items/:collection/:id
```

</info-box>

<info-box title="Request">

```json
{
  "title": "Welcome!"
}
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "id": 14,
    "status": "published",
    "title": "Welcome!",
    "body": "This is my first article",
    "featured_image": 2,
    "author": 5
  }
}
```

</info-box>
</div>
</template>
</two-up>

---

## Delete an Item

<two-up>
<template slot="left">

Delete an existing item

### Parameters

<def-list>

!!! include params/project.md !!!
!!! include params/collection.md !!!
!!! include params/id.md !!!

</def-list>

### Returns

Returns an empty body with HTTP status 204

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
DELETE /:project/items/:collection/:id
```

</info-box>
</div>
</template>
</two-up>

---

## List Item Revisions

<two-up>
<template slot="left">

List the revisions made to the given item.

### Parameters

<def-list>

!!! include params/project.md !!!
!!! include params/collection.md !!!
!!! include params/id.md !!!

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

Returns an array of [revision objects](/api/revisions.html#the-revision-object).

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /:project/items/:collection/:id/revisions
```

</info-box>
<info-box title="Response">

```json
{
  "data": [
    {
      "id": 35,
      "activity": 37,
      "collection": "articles",
      "item": "14",
      "data": {
        "id": 14,
        "status": "published",
        "title": "Hello, World!",
        "body": "This is my first article",
        "featured_image": 2,
        "author": 5
      },
      "delta": {
        "title": "Welcome!"
      },
      "parent_collection": null,
      "parent_item": null,
      "parent_changed": false
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

## Retrieve an Item Revision

<two-up>
<template slot="left">

Retrieve a single revision of the item by offset.

### Parameters

<def-list>

!!! include params/project.md !!!
!!! include params/collection.md !!!

#### offset <def-type alert>required</def-type>
How many revisions to go back in time.

</def-list>

### Query

<def-list>

!!! include query/fields.md !!!
!!! include query/meta.md !!!

</def-list>

### Returns

Returns the [revision object](/api/revisions.html#the-revision-object) for the given unique identifier.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /:project/items/:collection/:id/revisions/:offset
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "id": 35,
    "activity": 37,
    "collection": "articles",
    "item": "14",
    "data": {
      "id": 14,
      "status": "published",
      "title": "Hello, World!",
      "body": "This is my first article",
      "featured_image": 2,
      "author": 5
    },
    "delta": {
      "title": "Welcome!"
    },
    "parent_collection": null,
    "parent_item": null,
    "parent_changed": false
  }
}
```

</info-box>
</div>
</template>
</two-up>

---

## Revert to a Given Revision

<two-up>
<template slot="left">

Revert the item to a given revision.

### Parameters

<def-list>

!!! include params/project.md !!!
!!! include params/collection.md !!!
!!! include params/id.md !!!

#### revision <def-type alert>required</def-type>
Unique identifier of the revision to revert to.

</def-list>

### Attributes

No attributes available.

### Query

<def-list>

!!! include query/fields.md !!!
!!! include query/meta.md !!!

</def-list>

### Returns

Returns the [item object](#the-item-object) in its new state.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
 PATCH /:project/items/:collection/:id/revert/:revision
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "id": 14,
    "status": "published",
    "title": "Welcome!",
    "body": "This is my first article",
    "featured_image": 2,
    "author": 5
  }
}
```

</info-box>
</div>
</template>
</two-up>

---