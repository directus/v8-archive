---
pageClass: page-reference
---

# Folders

<two-up>

::: slot left
Folders don't do anything yet, but will be used in the (near) future to be able to group files.
:::

<info-box title="Endpoints" slot="right">

```endpoints
   GET /:project/folders
   GET /:project/folders/:id
  POST /:project/folders
 PATCH /:project/folders/:id
DELETE /:project/folders/:id
```

</info-box>
</two-up>

---

## The Folder Object

### Attributes

<two-up>
<template slot="left">
<def-list>

#### id <def-type>integer</def-type>
Unique identifier for the folder.

#### name <def-type>string</def-type>
Name of the folder.

#### parent_folder <def-type>integer</def-type>
Unique identifier of the parent folder. This allows for nested folders.

</def-list>
</template>

<info-box title="Folder Object" slot="right" class="sticky">

```json
{
  "id": 1,
  "name": "New York",
  "parent_folder": null
}
```

</info-box>
</two-up>

---

## List the Folders

<two-up>
<template slot="left">

List the folders.

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

Returns an array of [folder objects](#the-folder-object).

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /:project/folders
```

</info-box>
<info-box title="Response">

```json
{
  "data": [
    {
      "id": 1,
      "name": "New York",
      "parent_folder": null
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

## Retrieve a Folder

<two-up>
<template slot="left">

Retrieve a single folder by unique identifier.

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

Returns the [folder object](#the-folder-object) for the given unique identifier.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /:project/folders/:id
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "id": 1,
    "name": "New York",
    "parent_folder": null
  }
}
```

</info-box>
</div>
</template>
</two-up>

---

## Create a Folder

<two-up>
<template slot="left">

Create a new folder.

### Parameters

<def-list>

!!! include params/project.md !!!

</def-list>

### Attributes

<def-list>

#### name <def-type alert>required</def-type>
Name of the folder.

#### parent_folder <def-type>optional</def-type>
Unique identifier of the parent folder. This allows for nested folders.

</def-list>

### Query

<def-list>

!!! include query/fields.md !!!
!!! include query/meta.md !!!

</def-list>

### Returns

Returns the [folder object](#the-folder-object) for the folder that was just created.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
  POST /:project/folders
```

</info-box>

<info-box title="Request">

```json
{
  "name": "Amsterdam"
}
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "id": 5,
    "name": "Amsterdam",
    "parent_folder": null
  }
}
```

</info-box>
</div>
</template>
</two-up>

---

## Update a Folder

<two-up>
<template slot="left">

Update an existing folder

### Parameters

<def-list>

!!! include params/project.md !!!
!!! include params/id.md !!!

</def-list>

### Attributes

<def-list>

#### name <def-type alert>optional</def-type>
Name of the folder. Can't be null or empty.

#### parent_folder <def-type>optional</def-type>
Unique identifier of the parent folder. This allows for nested folders.

</def-list>

### Query

<def-list>

!!! include query/fields.md !!!
!!! include query/meta.md !!!

</def-list>

### Returns

Returns the [folder object](#the-folder-object) for the folder that was just updated.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
 PATCH /:project/folders/:id
```

</info-box>

<info-box title="Request">

```json
{
  "parent_folder": 3
}
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "id": 5,
    "name": "Amsterdam",
    "parent_folder": 3
  }
}
```

</info-box>
</div>
</template>
</two-up>

---

## Delete a Folder

<two-up>
<template slot="left">

Delete an existing folder

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
DELETE /:project/folders/:id
```

</info-box>
</div>
</template>
</two-up>

---