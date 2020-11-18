---
pageClass: page-reference
---

# Relations

<two-up>

::: slot left
What data is linked to what other data. Allows you to assign authors to articles, products to sales, and whatever other structures you can think of.
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

---

## The Relation Object

<two-up>
<template slot="left">
<def-list>

#### id <def-type>integer</def-type>
Unique identifier for the relation.

#### collection_many <def-type>string</def-type>
Collection that has the field that holds the foreign key.

#### field_many <def-type>string</def-type>
Foreign key. Field that holds the primary key of the related collection.

#### collection_one <def-type>string</def-type>
Collection on the _one_ side of the relationship.

#### field_one <def-type>string</def-type>
Alias column that serves as the _one_ side of the relationship.

#### junction_field <def-type>string</def-type>
Field on the junction table that holds the primary key of the related collection.

</def-list>
</template>

<info-box title="Relation Object" slot="right" class="sticky">

```json
{
  "id": 1,
  "collection_many": "directus_activity",
  "field_many": "action_by",
  "collection_one": "directus_users",
  "field_one": null,
  "junction_field": null
}
```

</info-box>
</two-up>

---

## List the Relations

<two-up>
<template slot="left">

List the relations.

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

Returns an array of [relation objects](#the-relation-object).

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /:project/relations
```

</info-box>
<info-box title="Response">

```json
{
  "data": [
    {
      "id": 1,
      "collection_many": "directus_activity",
      "field_many": "action_by",
      "collection_one": "directus_users",
      "field_one": null,
      "junction_field": null
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

## Retrieve a Relation

<two-up>
<template slot="left">

Retrieve a single relation by unique identifier.

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

Returns the [relation object](#the-relation-object) for the given unique identifier.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /:project/relations/:id
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "id": 1,
    "collection_many": "directus_activity",
    "field_many": "action_by",
    "collection_one": "directus_users",
    "field_one": null,
    "junction_field": null
  }
}
```

</info-box>
</div>
</template>
</two-up>

---

## Create a Relation

<two-up>
<template slot="left">

Create a new relation.

### Parameters

<def-list>

!!! include params/project.md !!!

</def-list>

### Attributes

<def-list>

#### collection_many <def-type alert>required</def-type>
Collection that has the field that holds the foreign key.

#### field_many <def-type alert>required</def-type>
Foreign key. Field that holds the primary key of the related collection.

#### collection_one <def-type>optional</def-type>
Collection on the _one_ side of the relationship.

#### field_one <def-type>optional</def-type>
Alias column that serves as the _one_ side of the relationship.

#### junction_field <def-type>optional</def-type>
Field on the junction table that holds the primary key of the related collection.

</def-list>

### Query

<def-list>

!!! include query/fields.md !!!
!!! include query/meta.md !!!

</def-list>

### Returns

Returns the [relation object](#the-relation-object) for the relation that was just created.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
  POST /:project/relations
```

</info-box>

<info-box title="Request">

```json
{
  "collection_many": "articles",
  "field_many": "author",
  "collection_one": "authors",
  "field_one": "books"
}
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "id": 15,
    "collection_many": "articles",
    "field_many": "author",
    "collection_one": "authors",
    "field_one": "books",
    "junction_field": null
  }
}
```

</info-box>
</div>
</template>
</two-up>

---

## Update a Relation

<two-up>
<template slot="left">

Update an existing relation

### Parameters

<def-list>

!!! include params/project.md !!!
!!! include params/id.md !!!

</def-list>

### Attributes

<def-list>

#### collection_many <def-type>optional</def-type>
Collection that has the field that holds the foreign key.

#### field_many <def-type>optional</def-type>
Foreign key. Field that holds the primary key of the related collection.

#### collection_one <def-type>optional</def-type>
Collection on the _one_ side of the relationship.

#### field_one <def-type>optional</def-type>
Alias column that serves as the _one_ side of the relationship.

#### junction_field <def-type>optional</def-type>
Field on the junction table that holds the primary key of the related collection.

</def-list>

### Query

<def-list>

!!! include query/fields.md !!!
!!! include query/meta.md !!!

</def-list>

### Returns

Returns the [relation object](#the-relation-object) for the relation that was just updated.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
 PATCH /:project/relations/:id
```

</info-box>

<info-box title="Request">

```json
{
  "field_one": "books"
}
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "id": 15,
    "collection_many": "articles",
    "field_many": "author",
    "collection_one": "authors",
    "field_one": "books",
    "junction_field": null
  }
}
```

</info-box>
</div>
</template>
</two-up>

---

## Delete a Relation

<two-up>
<template slot="left">

Delete an existing relation.

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
DELETE /:project/relations/:id
```

</info-box>
</div>
</template>
</two-up>

---