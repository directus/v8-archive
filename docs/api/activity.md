---
pageClass: page-reference
---

# Activity

<two-up>

::: slot left
All events that happen within Directus are tracked and stored in the activities collection. This gives you full accountability over everything that happens.
:::

<info-box title="Endpoints" slot="right">

```endpoints
   GET /:project/activity
   GET /:project/activity/:id
  POST /:project/activity/comment
 PATCH /:project/activity/comment/:id
DELETE /:project/activity/comment/:id
```

</info-box>
</two-up>

---

## The Activity Object

<two-up>

::: slot left
### Attributes

<def-list>

#### id <def-type>integer</def-type>
Unique identifier for the object.

#### action <def-type>string</def-type>
Action that was performed. One of `authenticate`, `comment`, `upload`, `create`, `update`, `delete`, `soft-delete`, `revert`, `status-update`, `invalid-credentials`.

#### action_by <def-type>integer</def-type>
Unique identifier of the user account who caused this action.

#### action_on <def-type>string (datetime)</def-type>
When the action happened.

#### ip <def-type>string</def-type>
The IP address of the user at the time the action took place.

#### user_agent <def-type>string</def-type>
User agent string of the browser the user used when the action took place.

#### collection <def-type>string</def-type>
Collection identifier in which the item resides.

#### item <def-type>string</def-type>
Unique identifier for the item the action applied to. This is always a string, even for integer primary keys.

#### edited_on <def-type>string (datetime)</def-type>
When the action record was edited. This currently only applies to comments, as activity records can't be modified.

#### comment <def-type>string</def-type>
User comment. This will store the comments that show up in the right sidebar of the item edit page in the admin app.

#### comment_deleted_on <def-type>string (datetime)</def-type>
When and if the comment was (soft-)deleted.

</def-list>

:::

<info-box title="Activity object" slot="right" class="sticky">

```json
{
  "id": 2,
  "action": "update",
  "action_by": 1,
  "action_on": "2019-12-05T22:52:18+00:00",
  "ip": "160.72.72.58",
  "user_agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36",
  "collection": "movies",
  "item": "328",
  "edited_on": null,
  "comment": null,
  "comment_deleted_on": null
}
```

</info-box>
</two-up>

---

## List Activity Actions

<two-up>

::: slot left
Returns a list of activity actions.

### Parameters

<def-list>

!!! include params/project.md !!!

</def-list>

### Query

<def-list>

!!! include query/fields.md !!!
!!! include query/limit.md !!!
!!! include query/meta.md !!!
!!! include query/offset.md !!!
!!! include query/single.md !!!
!!! include query/sort.md !!!
!!! include query/filter.md !!!
!!! include query/q.md !!!

</def-list>

### Returns

An object with a `data` property that contains an array of up to `limit` [activity objects](#the-activity-object). If no items are available, `data` will be an empty array.
:::

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
GET /:project/activity
```

</info-box>

<info-box title="Response">

```json
{
  "data": [
    {
      "id": 1,
      "action": "update",
      "action_by": 1,
      "action_on": "2019-12-05T22:52:09+00:00",
      "ip": "127.0.0.1",
      "user_agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36",
      "collection": "directus_fields",
      "item": "328",
      "edited_on": null,
      "comment": null,
      "comment_deleted_on": null
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

## Retrieve an Activity Action

<two-up>

::: slot left
Retrieves the details of an existing activity action. Provide the primary key of the activity action and Directus will return the corresponding information.

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

Returns an [activity object](#the-activity-object) if a valid identifier was provided.
:::

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
GET /:project/activity/:id
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "id": 15,
    "action": "update",
    "action_by": 1,
    "action_on": "2019-12-06T00:49:16+00:00",
    "ip": "127.0.0.1",
    "user_agent": "Mozilla\/5.0 (Macintosh; Intel Mac OS X 10_15_1) AppleWebKit\/537.36 (KHTML, like Gecko) Chrome\/78.0.3904.108 Safari\/537.36",
    "collection": "directus_fields",
    "item": "325",
    "edited_on": null,
    "comment": null,
    "comment_deleted_on": null
  }
}
```
</info-box>
</div>
</template>
:::

</two-up>

---

## Create a Comment

<two-up>

::: slot left
Creates a new comment.

### Parameters

<def-list>

!!! include params/project.md !!!

</def-list>

### Attributes

<def-list>

#### collection <def-type alert>required</def-type>
Parent collection of the item you want to comment on.

#### id <def-type alert>required</def-type>
Unique identifier of the item you want to comment on.

#### comment <def-type alert>required</def-type>
The comment to post on the item.

</def-list>

### Query

<def-list>

!!! include query/meta.md !!!

</def-list>

### Returns

Returns the [activity object](#the-activity-object) of the created comment.
:::

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
  POST /:project/activity/comment
```

</info-box>

<info-box title="Request body">

```json
{
  "collection": "projects",
  "item": 1,
  "comment": "A new comment"
}
```
</info-box>

<info-box title="Response">

```json
{
  "data": {
    "id": 23,
    "action": "comment",
    "action_by": 1,
    "action_on": "2020-01-10T20:37:01+00:00",
    "ip": "172.18.0.6",
    "user_agent": "Paw\/3.1.10 (Macintosh; OS X\/10.15.2) GCDHTTPRequest",
    "collection": "many",
    "item": "1",
    "edited_on": null,
    "comment": "My new comment",
    "comment_deleted_on": null
  }
}
```
</info-box>
</div>
</template>
:::

</two-up>

---

## Update a Comment

<two-up>

::: slot left
Update the content of an existing comment.

### Parameters

<def-list>

!!! include params/project.md !!!
!!! include params/id.md !!!

</def-list>

### Attributes

<def-list>

#### comment <def-type alert>required</def-type>
The updated comment text.

</def-list>

### Query

<def-list>

!!! include query/meta.md !!!

</def-list>

### Returns

Returns the [activity object](#the-activity-object) of the updated comment.
:::

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
 PATCH /:project/activity/comment/:id
```

</info-box>

<info-box title="Request body">

```json
{
  "comment": "My updated comment"
}
```
</info-box>

<info-box title="Response">

```json
{
  "data": {
    "id": 23,
    "action": "comment",
    "action_by": 1,
    "action_on": "2020-01-10T20:37:01+00:00",
    "ip": "172.18.0.6",
    "user_agent": "Paw\/3.1.10 (Macintosh; OS X\/10.15.2) GCDHTTPRequest",
    "collection": "many",
    "item": "1",
    "edited_on": "2020-01-10T20:37:17+00:00",
    "comment": "My updated comment",
    "comment_deleted_on": null
  }
}
```
</info-box>
</div>
</template>
:::

</two-up>

---

## Delete a Comment

<two-up>

::: slot left
Delete an existing comment. Deleted comments can not be retrieved.

### Parameters

<def-list>

!!! include params/project.md !!!
!!! include params/id.md !!!

</def-list>

### Returns

Returns an empty request body with status HTTP status 204.

:::

<info-box title="Endpoint" class="sticky" slot="right">

```endpoints
DELETE /:project/activity/comment/:id
```

</info-box>
</two-up>

---
