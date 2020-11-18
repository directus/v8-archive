---
pageClass: page-reference
---

# Extensions

<two-up>

::: slot left
Directus can easily be extended through the addition of several types of extensions, including layouts, interfaces, and modules.

[Open extensions reference](/api/extensions.html)
:::

<info-box title="Endpoints" slot="right">

```endpoints
   GET /interfaces
   GET /layouts
   GET /modules
```

</info-box>
</two-up>

---

## List Interfaces

<two-up>
<template slot="left">

List all installed custom interfaces.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /interfaces
```

</info-box>

<info-box title="Response">

```json
{
  "data": []
}
```
</info-box>
</div>
</template>
</two-up>

---

## List Layouts

<two-up>
<template slot="left">

List all installed custom layouts.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /layouts
```

</info-box>

<info-box title="Response">

```json
{
  "data": []
}
```
</info-box>
</div>
</template>
</two-up>

---

## List Modules

<two-up>
<template slot="left">

List all installed custom modules.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /modules
```

</info-box>

<info-box title="Response">

```json
{
  "data": []
}
```
</info-box>
</div>
</template>
</two-up>