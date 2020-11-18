---
pageClass: page-reference
---

# Collection Presets

<two-up>

::: slot left
Collection presets hold the preferences of individual users of the platform. This allows Directus to show and maintain custom item listings for users of the app.
:::

<info-box title="Endpoints" slot="right">

```endpoints
   GET /:project/collection_presets
   GET /:project/collection_presets/:id
  POST /:project/collection_presets
 PATCH /:project/collection_presets/:id
DELETE /:project/collection_presets/:id
```

</info-box>
</two-up>

---

## The Collection Preset Object

<two-up>

::: slot left
### Attributes

<def-list>

#### id <def-type>integer</def-type>
Unique identifier for this single collection preset.

#### title <def-type>string</def-type>
Name for the bookmark. If this is set, the collection preset will be considered to be a bookmark.

#### user <def-type>integer</def-type>
The unique identifier of the user to whom this collection preset applies.

#### role <def-type>integer</def-type>
The unique identifier of a role in the platform. If `user` is null, this will be used to apply the collection preset or bookmark for all users in the role.

#### collection <def-type>string</def-type>
What collection this collection preset is used for.

#### search_query <def-type>string</def-type>
What the user searched for in search/filter in the header bar.

#### filters <def-type>array</def-type>
The filters that the user applied.

#### view_type <def-type>string</def-type>
Name of the view type that is used.

#### view_query <def-type>object</def-type>
View query that's saved per view type. Controls what data is fetched on load. These follow the same format as the JS SDK parameters.

#### view_options <def-type>object</def-type>
Options of the views. The properties in here are controlled by the layout.

#### translation <def-type>object</def-type>
Key value pair of language-translation. Can be used to translate the bookmark title in multiple languages.

</def-list>

:::

<info-box title="Collection Preset Object" slot="right" class="sticky">

```json
{
  "id": 155,
  "title": null,
  "user": 1,
  "role": 1,
  "collection": "articles",
  "search_query": null,
  "filters": [
    {
      "field": "title",
      "operator": "contains",
      "value": "Hello"
    },
    { ... }
  ],
  "view_type": "timeline",
  "view_query": {
    "timeline": {
      "sort": "-published_on"
    }
  },
  "view_options": {
    "timeline": {
      "date": "published_on",
      "title": "{{ title }} ({{ author.first_name }} {{ author.last_name }})",
      "content": "excerpt",
      "color": "action"
    }
  },
  "translation": null
}
```

</info-box>
</two-up>

---

## List the Collection Presets

<two-up>
<template slot="left">

List the collection presets.

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

Returns an array of [collection preset objects](#the-collection-preset-object).

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /:project/collection_presets
```

</info-box>

<info-box title="Response">

```json
{
  "data": [
    {
      "id": 155,
      "title": null,
      "user": 1,
      "role": 1,
      "collection": "articles",
      "search_query": null,
      "filters": [
        {
          "field": "title",
          "operator": "contains",
          "value": "Hello"
        },
        { ... },
        { ... }
      ],
      "view_type": "timeline",
      "view_query": {
        "timeline": {
          "sort": "-published_on"
        }
      },
      "view_options": {
        "timeline": {
          "date": "published_on",
          "title": "{{ title }} ({{ author.first_name }} {{ author.last_name }})",
          "content": "excerpt",
          "color": "action"
        }
      },
      "translation": null
    }
  ]
}
```

</info-box>
</div>
</template>
</two-up>

---

## Retrieve a Collection Preset

<two-up>
<template slot="left">

Retrieve a single collection preset by unique identifier.

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

Returns the [collection preset object](#the-collection-preset-object) for the given unique identifier.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /:project/collection_presets/:id
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "id": 155,
    "title": null,
    "user": 1,
    "role": 1,
    "collection": "articles",
    "search_query": null,
    "filters": [
      {
        "field": "title",
        "operator": "contains",
        "value": "Hello"
      },
      { ... },
      { ... }
    ],
    "view_type": "timeline",
    "view_query": {
      "timeline": {
        "sort": "-published_on"
      }
    },
    "view_options": {
      "timeline": {
        "date": "published_on",
        "title": "{{ title }} ({{ author.first_name }} {{ author.last_name }})",
        "content": "excerpt",
        "color": "action"
      }
    },
    "translation": null
  }
}
```

</info-box>
</div>
</template>
</two-up>

---

## Create a Collection Preset

<two-up>
<template slot="left">

Create a new collection preset.

### Parameters

<def-list>

!!! include params/project.md !!!

</def-list>

### Attributes

<def-list>

#### collection <def-type alert>required</def-type>
What collection this collection preset is used for.

#### title <def-type>optional</def-type>
Name for the bookmark. If this is set, the collection preset will be considered to be a bookmark.

#### user <def-type>optional</def-type>
The unique identifier of the user to whom this collection preset applies.

#### role <def-type>optional</def-type>
The unique identifier of a role in the platform. If `user` is null, this will be used to apply the collection preset or bookmark for all users in the role.

#### search_query <def-type>optional</def-type>
What the user searched for in search/filter in the header bar.

#### filters <def-type>optional</def-type>
The filters that the user applied.

#### view_type <def-type>optional</def-type>
Name of the view type that is used. Defaults to `tabular`.

#### view_query <def-type>optional</def-type>
View query that's saved per view type. Controls what data is fetched on load. These follow the same format as the JS SDK parameters.

#### view_options <def-type>optional</def-type>
Options of the views. The properties in here are controlled by the layout.

#### translation <def-type>optional</def-type>
Key value pair of language-translation. Can be used to translate the bookmark title in multiple languages.

</def-list>

### Query

<def-list>

!!! include query/fields.md !!!
!!! include query/meta.md !!!

</def-list>

### Returns

Returns the [collection preset object](#the-collection-preset-object) for the collection preset that was just created.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
  POST /:project/collection_presets
```

</info-box>

<info-box title="Request">

```json
{
  "collection": "articles",
  "title": "Highly rated articles",
  "filters": [{
  	"field": "rating",
  	"operator": "gte",
  	"value": 4.5
  }]
}
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "id": 29,
    "title": "Highly rated articles",
    "user": null,
    "role": null,
    "collection": "articles",
    "search_query": null,
    "filters": [
      {
        "field": "rating",
        "operator": "gte",
        "value": 4.5
      }
    ],
    "view_type": "tabular",
    "view_query": null,
    "view_options": null,
    "translation": null
  }
}
```

</info-box>
</div>
</template>
</two-up>

---

## Update a Collection Preset

<two-up>
<template slot="left">

Update an existing collection preset.

### Parameters

<def-list>

!!! include params/project.md !!!
!!! include params/id.md !!!

</def-list>

### Attributes

<def-list>

#### collection <def-type alert>required</def-type>
What collection this collection preset is used for.

#### title <def-type>optional</def-type>
Name for the bookmark. If this is set, the collection preset will be considered to be a bookmark.

#### user <def-type>optional</def-type>
The unique identifier of the user to whom this collection preset applies.

#### role <def-type>optional</def-type>
The unique identifier of a role in the platform. If `user` is null, this will be used to apply the collection preset or bookmark for all users in the role.

#### search_query <def-type>optional</def-type>
What the user searched for in search/filter in the header bar.

#### filters <def-type>optional</def-type>
The filters that the user applied.

#### view_type <def-type>optional</def-type>
Name of the view type that is used. Defaults to `tabular`.

#### view_query <def-type>optional</def-type>
View query that's saved per view type. Controls what data is fetched on load. These follow the same format as the JS SDK parameters.

#### view_options <def-type>optional</def-type>
Options of the views. The properties in here are controlled by the layout.

#### translation <def-type>optional</def-type>
Key value pair of language-translation. Can be used to translate the bookmark title in multiple languages.

</def-list>

### Query

<def-list>

!!! include query/fields.md !!!
!!! include query/meta.md !!!

</def-list>

### Returns

A [collection preset object](#the-collection-preset-object).

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
 PATCH /:project/collection_presets/:id
```

</info-box>

<info-box title="Request">

```json
{
  "collection": "movies"
}
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "id": 29,
    "title": "Highly rated articles",
    "user": null,
    "role": null,
    "collection": "movies",
    "search_query": null,
    "filters": [
      {
        "field": "rating",
        "operator": "gte",
        "value": 4.5
      }
    ],
    "view_type": "tabular",
    "view_query": null,
    "view_options": null,
    "translation": null
  }
}
```

</info-box>
</div>
</template>
</two-up>

---

## Delete a Collection Preset

<two-up>
<template slot="left">

Delete an existing collection preset.

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
DELETE /:project/collection_presets/:id
```

</info-box>
</div>
</template>
</two-up>

---