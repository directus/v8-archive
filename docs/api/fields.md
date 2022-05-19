---
pageClass: page-reference
---

:::danger Legacy Version
These are the docs for Directus 8, a legacy version of the platform. If you're looking for the current Directus 9 documentation, go here: [https://docs.directus.io](https://docs.directus.io)
:::

# Fields

<two-up>

::: slot left
Fields are individual pieces of content within an item. They are mapped to columns in the database.
:::

<info-box title="Endpoints" slot="right">

```endpoints
   GET /:project/fields
   GET /:project/fields/:collection
   GET /:project/fields/:collection/:field
  POST /:project/fields/:collection
 PATCH /:project/fields/:collection/:field
DELETE /:project/fields/:collection/:field
```

</info-box>
</two-up>

---

## The Field Object

<two-up>

::: slot left
### Attributes

<def-list>

#### collection <def-type>string</def-type>
Unique name of the collection this field is in.

#### field <def-type>string</def-type>
Unique name of the field. Field name is unique within the collection.

#### datatype <def-type>string</def-type>
SQL datatype of the column that corresponds to this field.

#### unique <def-type>boolean</def-type>
If the value of this field should be unique within the collection.

#### primary_key <def-type>boolean</def-type>
If this field is the primary key of the collection.

#### auto_increment <def-type>boolean</def-type>
If the value in this field is auto incremented. Only applies to integer type fields.

#### default_value <def-type>value</def-types>
The default value for the field. Used when a specific value is not provided during item creation. The default value's type should be consistent with the `type` attribute.

#### note <def-type>string</def-type>
A user provided note for the field. Will be rendered alongside the interface on the edit page.

#### signed <def-type>boolean</def-type>
If the value is signed or not. Only applies to integer type fields.

#### id <def-type>integer</def-type>
Unique identifier for the field in the `directus_fields` collection.

#### type <def-type>string</def-type>
Directus specific data type. Used to cast values in the API.

#### sort <def-type>integer</def-type>
Sort order of this field on the edit page of the admin app.

#### interface <def-type>string</def-type>
What interface is used in the admin app to edit the value for this field.

#### hidden_detail <def-type>boolean</def-type>
If this field should be hidden from the item detail (edit) page.

#### hidden_browse <def-type>boolean</def-type>
If this field should be hidden from the item browse (listing) page.

#### required <def-type>boolean</def-type>
If this field requires a value.

#### options <def-type>object</def-type>
Options for the interface that's used. This format is based on the individual interface.

#### locked <def-type>boolean</def-type>
If the field can be altered by the end user. Directus system fields have this value set to `true`.

#### translation <def-type>key/value</def-type>
Key value pair of `<locale>: <translation>` that allows the user to change the displayed name of the field in the admin app.

#### readonly <def-type>boolean</def-type>
Prevents the user from editing the value in the field.

#### width <def-type>string</def-type>
Width of the field on the edit form. One of `half`, `half-left`, `half-right`, `full`, `fill`.

#### validation <def-type>regex</def-type>
User provided regex that will be used in the API to validate incoming values. It uses the PHP flavor of RegEX.

#### group <def-type>integer</def-type>
What field group this field is part of.

#### length <def-type>integer</def-type>
Length of the field. Will be used in SQL to set the `length` property of the colummn.

</def-list>

:::

<info-box title="Collection object" slot="right" class="sticky">

```json
{
  "collection": "about_us",
  "field": "id",
  "datatype": "INT",
  "unique": false,
  "primary_key": true,
  "auto_increment": true,
  "default_value": null,
  "note": "",
  "signed": false,
  "id": 167,
  "type": "integer",
  "sort": 1,
  "interface": "primary-key",
  "hidden_detail": true,
  "hidden_browse": true,
  "required": true,
  "options": null,
  "locked": 0,
  "translation": null,
  "readonly": false,
  "width": null,
  "validation": null,
  "group": null,
  "length": "10"
}
```

</info-box>
</two-up>

---

## List Fields

<two-up>

::: slot left
Returns a list of the fields available in the project.

### Parameters

<def-list>

!!! include params/project.md !!!

</def-list>

### Query

<def-list>

!!! include query/limit.md !!!
!!! include query/sort.md !!!

</def-list>

### Returns

An object with a `data` property that contains an array [field objects](#the-field-object) for the available fields. This array also contains the Directus system fields and is never empty.
:::

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
GET /:project/fields
```

</info-box>

<info-box title="Response">

```json
{
  "data": [
  	{
      "collection": "about_us",
      "field": "id",
      "datatype": "INT",
      "unique": false,
      "primary_key": true,
      "auto_increment": true,
      "default_value": null,
      "note": "",
      "signed": false,
      "id": 167,
      "type": "integer",
      "sort": 1,
      "interface": "primary-key",
      "hidden_detail": true,
      "hidden_browse": true,
      "required": true,
      "options": null,
      "locked": 0,
      "translation": null,
      "readonly": false,
      "width": null,
      "validation": null,
      "group": null,
      "length": "10"
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

## List Fields in Collection

<two-up>

::: slot left
Returns a list of the fields available in the given collection.

### Parameters

<def-list>

!!! include params/project.md !!!
!!! include params/collection.md !!!

</def-list>

### Query

<def-list>

!!! include query/sort.md !!!

</def-list>

### Returns

An object with a `data` property that contains an array of [field objects](#the-field-object) for available fields.
:::

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
GET /:project/fields/:collection
```

</info-box>

<info-box title="Response">

```json
{
  "data": [
    {
      "collection": "about_us",
      "field": "id",
      "datatype": "INT",
      "unique": false,
      "primary_key": true,
      "auto_increment": true,
      "default_value": null,
      "note": "",
      "signed": false,
      "id": 167,
      "type": "integer",
      "sort": 1,
      "interface": "primary-key",
      "hidden_detail": true,
      "hidden_browse": true,
      "required": true,
      "options": null,
      "locked": 0,
      "translation": null,
      "readonly": false,
      "width": null,
      "validation": null,
      "group": null,
      "length": "10"
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

## Retrieve a Field

<two-up>

::: slot left
Retrieves the details of a single field in a given collection.

### Parameters

<def-list>

!!! include params/project.md !!!
!!! include params/collection.md !!!

#### field <def-type alert>required</def-type>
The unique name of the field.

</def-list>

### Query

No query parameters available.

### Returns

Returns a [field object](#the-field-object).
:::

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
GET /:project/fields/:collection/:field
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "collection": "about_us",
    "field": "id",
    "datatype": "INT",
    "unique": false,
    "primary_key": true,
    "auto_increment": true,
    "default_value": null,
    "note": "",
    "signed": false,
    "id": 167,
    "type": "integer",
    "sort": 1,
    "interface": "primary-key",
    "hidden_detail": true,
    "hidden_browse": true,
    "required": true,
    "options": null,
    "locked": 0,
    "translation": null,
    "readonly": false,
    "width": null,
    "validation": null,
    "group": null,
    "length": "10"
  }
}
```
</info-box>
</div>
</template>
:::

</two-up>

---

## Create a Field

<two-up>
<template slot="left">

Create a new field in a given collection.

### Parameters

<def-list>

!!! include params/project.md !!!
!!! include params/collection.md !!!

</def-list>

### Attributes

<def-list>

#### field <def-type alert>required</def-type>
Unique name of the field. Field name is unique within the collection.

#### datatype <def-type alert>required</def-type>
SQL datatype of the column that corresponds to this field.

#### unique <def-type>optional</def-type>
If the value of this field should be unique within the collection.

#### primary_key <def-type>optional</def-type>
If this field is the primary key of the collection.

#### auto_increment <def-type>optional</def-type>
If the value in this field is auto incremented. Only applies to integer type fields.

#### default_value <def-type>optional</def-types>
The default value for the field. Used when a specific value is not provided during item creation. The default value's type should be consistent with the `type` attribute.

#### note <def-type>optional</def-type>
A user provided note for the field. Will be rendered alongside the interface on the edit page.

#### signed <def-type>optional</def-type>
If the value is signed or not. Only applies to integer type fields.

#### type <def-type alert>required</def-type>
Directus specific data type. Used to cast values in the API.

#### sort <def-type>optional</def-type>
Sort order of this field on the edit page of the admin app.

#### interface <def-type>optional</def-type>
What interface is used in the admin app to edit the value for this field.

#### hidden_detail <def-type>optional</def-type>
If this field should be hidden from the item detail (edit) page.

#### hidden_browse <def-type>optional</def-type>
If this field should be hidden from the item browse (listing) page.

#### required <def-type>optional</def-type>
If this field requires a value.

#### options <def-type>optional</def-type>
Options for the interface that's used. This format is based on the individual interface.

#### locked <def-type>optional</def-type>
If the field can be altered by the end user. Directus system fields have this value set to `true`.

#### translation <def-type>optional/value</def-type>
Key value pair of `<locale>: <translation>` that allows the user to change the displayed name of the field in the admin app.

#### readonly <def-type>optional</def-type>
Prevents the user from editing the value in the field.

#### width <def-type>optional</def-type>
Width of the field on the edit form. One of `half`, `half-left`, `half-right`, `full`, `fill`.

#### validation <def-type>optional</def-type>
User provided regex that will be used in the API to validate incoming values.

#### group <def-type>optional</def-type>
What field group this field is part of.

#### length <def-type alert>required</def-type>
Length of the field. Will be used in SQL to set the `length` property of the colummn. Requirement of this attribute depends on the provided `datatype`.

</def-list>

### Query

No query parameters available.

### Returns

Returns a [field object](#the-field-object) for the newly created field.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
  POST /:project/fields/:collection
```

</info-box>

<info-box title="Request body">

```json
{
  "field": "test",
  "type": "string",
  "datatype": "VARCHAR",
  "length": 255,
  "interface": "text-input"
}
```
</info-box>

<info-box title="Response">

```json
{
  "data": {
    "collection": "about_us",
    "field": "title",
    "datatype": "VARCHAR",
    "unique": false,
    "primary_key": false,
    "auto_increment": false,
    "default_value": null,
    "note": null,
    "signed": true,
    "id": 895,
    "type": "string",
    "sort": 0,
    "interface": "text-input",
    "hidden_detail": false,
    "hidden_browse": false,
    "required": false,
    "options": null,
    "locked": false,
    "translation": null,
    "readonly": false,
    "width": null,
    "validation": null,
    "group": null,
    "length": "255"
  }
}
```
</info-box>
</div>
</template>
:::

</two-up>

---

## Update a Field

<two-up>
<template slot="left">

Update an existing field.

::: warning
You can't update a field's name.
:::

### Parameters

<def-list>

!!! include params/project.md !!!
!!! include params/collection.md !!!

#### field <def-type alert>required</def-type>
The unique name of the field.

</def-list>

### Attributes

<def-list>

#### datatype <def-type>optional</def-type>
SQL datatype of the column that corresponds to this field.

#### unique <def-type>optional</def-type>
If the value of this field should be unique within the collection.

#### primary_key <def-type>optional</def-type>
If this field is the primary key of the collection.

#### auto_increment <def-type>optional</def-type>
If the value in this field is auto incremented. Only applies to integer type fields.

#### default_value <def-type>optional</def-types>
The default value for the field. Used when a specific value is not provided during item creation. The default value's type should be consistent with the `type` attribute.

#### note <def-type>optional</def-type>
A user provided note for the field. Will be rendered alongside the interface on the edit page.

#### signed <def-type>optional</def-type>
If the value is signed or not. Only applies to integer type fields.

#### type <def-type>optional</def-type>
Directus specific data type. Used to cast values in the API.

#### sort <def-type>optional</def-type>
Sort order of this field on the edit page of the admin app.

#### interface <def-type>optional</def-type>
What interface is used in the admin app to edit the value for this field.

#### hidden_detail <def-type>optional</def-type>
If this field should be hidden from the item detail (edit) page.

#### hidden_browse <def-type>optional</def-type>
If this field should be hidden from the item browse (listing) page.

#### required <def-type>optional</def-type>
If this field requires a value.

#### options <def-type>optional</def-type>
Options for the interface that's used. This format is based on the individual interface.

#### locked <def-type>optional</def-type>
If the field can be altered by the end user. Directus system fields have this value set to `true`.

#### translation <def-type>optional/value</def-type>
Key value pair of `<locale>: <translation>` that allows the user to change the displayed name of the field in the admin app.

#### readonly <def-type>optional</def-type>
Prevents the user from editing the value in the field.

#### width <def-type>optional</def-type>
Width of the field on the edit form. One of `half`, `half-left`, `half-right`, `full`, `fill`.

#### validation <def-type>optional</def-type>
User provided regex that will be used in the API to validate incoming values.

#### group <def-type>optional</def-type>
What field group this field is part of.

#### length <def-type>optional</def-type>
Length of the field. Will be used in SQL to set the `length` property of the colummn. Requirement of this attribute depends on the provided `datatype`.

</def-list>

::: warning datatype
Be careful when updating the datatype. You could corrupt the data in the items. Be sure to make a backup of the database if you're not sure of what you're doing.
:::

### Query

No query parameters available.

### Returns
Returns the [field object](#the-field-object) for the updated field.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
 PATCH /:project/fields/:collection/:field
```

</info-box>

<info-box title="Request body">

```json
{
  "note": "Enter the title here."
}
```
</info-box>

<info-box title="Response">

```json
{
  "data": {
    "collection": "about_us",
    "field": "title",
    "datatype": "VARCHAR",
    "unique": false,
    "primary_key": false,
    "auto_increment": false,
    "default_value": null,
    "note": "Enter the title here",
    "signed": true,
    "id": 895,
    "type": "string",
    "sort": 0,
    "interface": "text-input",
    "hidden_detail": false,
    "hidden_browse": false,
    "required": false,
    "options": null,
    "locked": false,
    "translation": null,
    "readonly": false,
    "width": null,
    "validation": null,
    "group": null,
    "length": "255"
  }
}
```

</info-box>
</div>
</template>
</two-up>

---

## Delete a Field

<two-up>
<template slot="left">

Delete an existing field.

::: danger
This will delete the whole field, including the values within. Proceed with caution.
:::

### Parameters

<def-list>

!!! include params/project.md !!!
!!! include params/collection.md !!!

#### field <def-type alert>required</def-type>
The unique name of the field.

</def-list>

### Returns

Returns an empty body with HTTP status 204

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
DELETE /:project/fields/:collection/:field
```

</info-box>
</div>
</template>
</two-up>

---
