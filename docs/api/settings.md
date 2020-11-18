---
pageClass: page-reference
---

# Settings

<two-up>

::: slot left
Settings control the way the platform works and acts.
:::

<info-box title="Endpoints" slot="right">

```endpoints
   GET /:project/settings
   GET /:project/settings/:id
  POST /:project/settings
 PATCH /:project/settings/:id
DELETE /:project/settings/:id
```

</info-box>
</two-up>

---

## The Setting Object

### Attributes

<two-up>
<template slot="left">
<def-list>

#### id <def-type>integer</def-type>
Unique identifier for the setting.

#### key <def-type>string</def-type>
Name of the setting

#### value <def-type>string</def-type>
Value of the setting

</def-list>
</template>

<info-box title="Setting Object" slot="right" class="sticky">

```json
{
  "id": 1,
  "key": "project_color",
  "value": "#abcabc"
}
```

</info-box>
</two-up>

---

## List the Settings

<two-up>
<template slot="left">

List the settings.

### Parameters

<def-list>

!!! include params/project.md !!!

</def-list>

### Query

<def-list>

!!! include query/limit.md !!!
!!! include query/offset.md !!!
!!! include query/page.md !!!
!!! include query/single.md !!!
!!! include query/meta.md !!!

</def-list>

### Returns

Returns an array of [setting objects](#the-setting-object).

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /:project/settings
```

</info-box>
<info-box title="Response">

```json
{
  "data": [
    {
      "id": 1,
      "key": "project_url",
      "value": ""
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

## Retrieve a Setting

<two-up>
<template slot="left">

Retrieve a single setting by unique identifier.

### Parameters

<def-list>

!!! include params/project.md !!!
!!! include params/id.md !!!

</def-list>

### Query

<def-list>

!!! include query/meta.md !!!

</def-list>

### Returns

Returns the [setting object](#the-setting-object) for the given unique identifier.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /:project/settings/:id
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "id": 1,
    "key": "project_color",
    "value": "#abcabc"
  }
}
```

</info-box>
</div>
</template>
</two-up>

---

## Create a Setting

<two-up>
<template slot="left">

Create a new setting.

### Parameters

<def-list>

!!! include params/project.md !!!

</def-list>

### Attributes

<def-list>

#### key <def-type alert>required</def-type>
Key for the setting

#### value <def-type>optional</def-type>
Value for the setting

</def-list>

### Query

<def-list>

!!! include query/meta.md !!!

</def-list>

### Returns

Returns the [setting object](#the-setting-object) for the setting that was just created.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
  POST /:project/settings
```

</info-box>

<info-box title="Request">

```json
{
  "key": "my_custom_setting"
}
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "id": 23,
    "key": "my_custom_setting",
    "value": null
  }
}
```

</info-box>
</div>
</template>
</two-up>

---

## Update a Setting

<two-up>
<template slot="left">

Update an existing setting

### Parameters

<def-list>

!!! include params/project.md !!!
!!! include params/id.md !!!

</def-list>

### Attributes

<def-list>

#### key <def-type alert>optional</def-type>
Key for the setting

#### value <def-type>optional</def-type>
Value for the setting

</def-list>

### Query

<def-list>

!!! include query/meta.md !!!

</def-list>

### Returns

Returns the [setting object](#the-setting-object) for the setting that was just updated.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
 PATCH /:project/settings/:id
```

</info-box>

<info-box title="Request">

```json
{
  "value": "15"
}
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "id": 23,
    "key": "my_custom_setting",
    "value": "15"
  }
}
```

</info-box>
</div>
</template>
</two-up>

---

## Delete a Setting

<two-up>
<template slot="left">

Delete an existing setting

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
DELETE /:project/settings/:id
```

</info-box>
</div>
</template>
</two-up>

---