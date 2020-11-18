---
pageClass: page-reference
---

# Assets (Thumbnails)

<two-up>

::: slot left
Image typed files can be dynamically resized and transformed to fit any need.
:::

<info-box title="Endpoints" slot="right">

```endpoints
   GET /:project/assets/:key
```

</info-box>
</two-up>

---

## Get an asset

<two-up>
<template slot="left">

Get a resized file. Files are resized on demand.

### Parameters

<def-list>

!!! include params/project.md !!!

#### key <def-type alert>required</def-type>
`private_hash` of the file.

</def-list>

### Query

<def-list>

#### key <def-type>optional</def-type>
The key of the asset size configured in settings.

#### w <def-type>optional</def-type>
Width of the file in pixels.

#### h <def-type>optional</def-type>
Height of the file in pixels.

#### f <def-type>optional</def-type>
Fit. One of `crop`, `contain`.

#### q <def-type>optional</def-type>
Quality of compression. Number between 1 and 100.

</def-list>

If you're using `key`, you don't need to specify any of the other query parameters. If you're using `w`, `h`, or any of the others, you're required to provide them all. This allows Directus to match it against the whitelist in settings. If you have the whitelist turned off completely, any combination is allowed.

</template>
<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /:project/assets/:key
```

</info-box>

</div>
</template>
</two-up>

---