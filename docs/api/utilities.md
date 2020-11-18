---
pageClass: page-reference
---

# Utilities

<two-up>

::: slot left
Directus comes with various utility endpoints you can use to simplify your development flow.
:::

<info-box title="Endpoints" slot="right">

```endpoints
  POST /:project/utils/hash
  POST /:project/utils/hash/match
  POST /:project/utils/random/string
   GET /:project/utils/2fa_secret
```

</info-box>
</two-up>

---

## Create a Hash

<two-up>
<template slot="left">

Create a hash for a given string.

### Parameters

<def-list>

!!! include params/project.md !!!

</def-list>

### Attributes

<def-list>

#### string <def-type alert>required</def-type>
String you want to hash.

</def-list>

### Query

No query parameters available.

### Returns

Returns the hash for the string.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
  POST /:project/utils/hash
```

</info-box>

<info-box title="Request">

```json
{
  "string": "Directus"
}
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "hash": "$2y$10$yBKRgLWmGnrPxi4WXec/0eVkoJNZoNGufbmD38qSZMZnVtq47.tBi"
  }
}
```

</info-box>
</div>
</template>
</two-up>

---

## Verify a Hashed String

<two-up>
<template slot="left">

Check if a hash is valid for a given string.

### Parameters

<def-list>

!!! include params/project.md !!!

</def-list>

### Attributes

<def-list>

#### string <def-type alert>required</def-type>
String you want to hash.

#### hash <def-type alert>required</def-type>
The hash you want to verify.

</def-list>

### Query

No query parameters available.

### Returns

Returns a boolean called `valid`.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
  POST /:project/utils/hash/match
```

</info-box>

<info-box title="Request">

```json
{
  "hash": "$2y$10$yBKRgLWmGnrPxi4WXec/0eVkoJNZoNGufbmD38qSZMZnVtq47.tBi",
  "string": "Directus"
}
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "valid": true
  }
}
```

</info-box>
</div>
</template>
</two-up>

---

## Generate a Random String

<two-up>
<template slot="left">

Returns a random string of given length.

### Parameters

<def-list>

!!! include params/project.md !!!

</def-list>

### Attributes

<def-list>

#### length <def-type>optional</def-type>
How long the string should be. Defaults to 32.

</def-list>

### Query

No query parameters available.

### Returns

Returns a boolean called `valid`.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
  POST /:project/utils/random/string
```

</info-box>

<info-box title="Request">

```json
{
  "length": 10
}
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "random": "1>M3+4oh.S"
  }
}
```

</info-box>
</div>
</template>
</two-up>

---

## Generate a 2FA Secret

<two-up>
<template slot="left">

Returns a random string that can be used as a 2FA secret

### Parameters

<def-list>

!!! include params/project.md !!!

</def-list>

### Query

No query parameters available.

### Returns

Returns a boolean called `valid`.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /:project/utils/2fa_secret
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "2fa_secret": "NWLNVDRK7VKMG3VY"
  }
}
```

</info-box>
</div>
</template>
</two-up>

---
