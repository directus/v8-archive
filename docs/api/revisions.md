---
pageClass: page-reference
---

# Revisions

<two-up>

::: slot left
Revisions are individual changes to items made. Directus keeps track of changes made, so you're able to revert to a previous state at will.
:::

<info-box title="Endpoints" slot="right">

```endpoints
   GET /:project/revisions
   GET /:project/revisions/:id
```

</info-box>
</two-up>

---

## The Revision Object

### Attributes

<two-up>
<template slot="left">
<def-list>

#### id <def-type>integer</def-type>
Unique identifier for the revision.

#### activity <def-type>integer</def-type>
Unique identifier for the [activity](/api/activity) record.

#### collection <def-type>string</def-type>
Collection of the updated item.

#### item <def-type>string</def-type>
Primary key of updated item.

#### data <def-type>object</def-type>
Copy of item state at time of update.

#### delta <def-type>object</def-type>
Changes between the previous and the current revision.

#### parent_collection <def-type>string</def-type>
If the current item was updated relationally, this is the collection of the parent item.

#### parent_item <def-type>string</def-type>
If the current item was updated relationally, this is the unique identifier of the parent item.

#### parent_changed <def-type>boolean</def-type>
If the current item was updated relationally, this shows if the parent item was updated as well.

</def-list>
</template>

<info-box title="Revision Object" slot="right" class="sticky">

```json
{
  "id": 1,
  "activity": 2,
  "collection": "articles",
  "item": "168",
  "data": {
    "id": "168",
    "title": "Hello, World!",
    "body": "This is my first post",
    "author": 1,
    "featured_image": 15
  },
  "delta": {
    "title": "Hello, World!"
  },
  "parent_collection": null,
  "parent_item": null,
  "parent_changed": false
}
```

</info-box>
</two-up>

---

## List the Revisions

<two-up>
<template slot="left">

List the revisions.

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

Returns an array of [revision objects](#the-revision-object).

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /:project/revisions
```

</info-box>
<info-box title="Response">

```json
{
  "data": [
    {
      "id": 1,
      "activity": 2,
      "collection": "articles",
      "item": "168",
      "data": {
        "id": "168",
        "title": "Hello, World!",
        "body": "This is my first post",
        "author": 1,
        "featured_image": 15
      },
      "delta": {
        "title": "Hello, World!"
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

## Retrieve a Revision

<two-up>
<template slot="left">

Retrieve a single revision by unique identifier.

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

Returns the [revision object](#the-revision-object) for the given unique identifier.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /:project/revisions/:id
```

</info-box>

<info-box title="Response">

```json
{
  "id": 1,
  "activity": 2,
  "collection": "articles",
  "item": "168",
  "data": {
    "id": "168",
    "title": "Hello, World!",
    "body": "This is my first post",
    "author": 1,
    "featured_image": 15
  },
  "delta": {
    "title": "Hello, World!"
  },
  "parent_collection": null,
  "parent_item": null,
  "parent_changed": false
}
```

</info-box>
</div>
</template>
</two-up>

---