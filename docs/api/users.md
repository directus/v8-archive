---
pageClass: page-reference
---

# Users

<two-up>

::: slot left
Users are what gives you access to the data.
:::

<info-box title="Endpoints" slot="right">

```endpoints
   GET /:project/users
   GET /:project/users/:id
   GET /:project/users/me
  POST /:project/users
 PATCH /:project/users/:id
DELETE /:project/users/:id
  POST /:project/users/invite
  POST /:project/users/invite/:token
 PATCH /:project/users/:id/tracking/page
   GET /:project/users/:id/revisions
   GET /:project/users/:id/revisions/:offset
```

</info-box>
</two-up>

---

## The User Object

### Attributes

<two-up>
<template slot="left">
<def-list>

#### id <def-type>integer</def-type>
Unique identifier for the user.

#### status <def-type>string</def-type>
One of `active`, `invited`, `draft`, `suspended`, `deleted`.

#### role <def-type>integer</def-type>
Unique identifier of the role of this user.

#### first_name <def-type>string</def-type>
First name of the user.

#### last_name <def-type>string</def-type>
First name of the user.

#### email <def-type>string</def-type>
Unique email address for the user.

#### last_access_on <def-type>datetime</def-type>
When this user logged in last.

#### last_page <def-type>string</def-type>
Last page that the user was on.

#### external_id <def-type>string</def-type>
ID used for SCIM.

#### theme <def-type>string</def-type>
What theme the user is using. One of `light`, `dark`, or `auto`.

#### 2fa_secret <def-type>string</def-type>
The 2FA secret string that's used to generate one time passwords.

#### password_reset_token <def-type>string</def-type>
IF the users requests a password reset, this token will be sent in an email.

#### timezone <def-type>string</def-type>
The user's timezone.

#### locale <def-type>string</def-type>
The user's locale used in Directus.

#### locale_options <def-type>object</def-type>
Not currently used. Can be used in the future to allow language overrides like different date formats for locales.

#### avatar <def-type>file object</def-type>
The user's avatar.

#### company <def-type>string</def-type>
The user's company.

#### title <def-type>string</def-type>
The user's title.

#### email_notifications <def-type>boolean</def-type>
Whether or not the user wants to receive notifications per email.

</def-list>

::: tip
The user's (hashed) `password` will never be returned by the API.
:::

</template>

<info-box title="User Object" slot="right" class="sticky">

```json
{
  "id": 1,
  "status": "active",
  "role": 1,
  "first_name": "Admin",
  "last_name": "User",
  "email": "admin@example.com",
  "token": "admin",
  "last_access_on": "2020-01-13T19:55:18+00:00",
  "last_page": "/my-project/settings/collections/a",
  "external_id": null,
  "theme": "auto",
  "2fa_secret": null,
  "password_reset_token": null,
  "timezone": "America/New_York",
  "locale": "en-US",
  "locale_options": null,
  "avatar": null,
  "company": null,
  "title": null,
  "email_notifications": true
}
```

</info-box>
</two-up>

---

## List the users

<two-up>
<template slot="left">

List the users.

### Parameters

<def-list>

!!! include params/project.md !!!

</def-list>

### Query

<def-list>

!!! include query/fields.md !!!
!!! include query/limit.md !!!
!!! include query/offset.md !!!
!!! include query/sort.md !!!
!!! include query/single.md !!!
!!! include query/status.md !!!
!!! include query/filter.md !!!
!!! include query/q.md !!!
!!! include query/meta.md !!!

</def-list>

### Returns

Returns an array of [user objects](#the-user-object).

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /:project/users
```

</info-box>
<info-box title="Response">

```json
{
  "data": [
    {
      "id": 1,
      "status": "active",
      "role": 1,
      "first_name": "Admin",
      "last_name": "User",
      "email": "admin@example.com",
      "token": "admin",
      "last_access_on": "2020-01-13T21:58:56+00:00",
      "last_page": "/my-project/settings/collections/a",
      "external_id": null,
      "theme": "auto",
      "2fa_secret": null,
      "password_reset_token": null,
      "timezone": "America/New_York",
      "locale": "en-US",
      "locale_options": null,
      "avatar": null,
      "company": null,
      "title": null,
      "email_notifications": true
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

## Retrieve a User

<two-up>
<template slot="left">

Retrieve a single user by unique identifier.

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

Returns the [user object](#the-user-object) for the given unique identifier.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /:project/users/:id
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "id": 5,
    "status": "draft",
    "role": 1,
    "first_name": "Admin",
    "last_name": "User",
    "email": "admin2@example.com",
    "token": null,
    "last_access_on": "2020-01-13T19:55:18+00:00",
    "last_page": "/my-project/settings/collections/a",
    "external_id": null,
    "theme": "auto",
    "2fa_secret": null,
    "password_reset_token": null,
    "timezone": "America/New_York",
    "locale": "en-US",
    "locale_options": null,
    "avatar": null,
    "company": null,
    "title": null,
    "email_notifications": true
  }
}
```

</info-box>
</div>
</template>
</two-up>

---

## Retrieve the Current User

<two-up>
<template slot="left">

Retrieve the currently authenticated user.

::: tip
This endpoint doesn't work for the public role.
:::

### Parameters

<def-list>

!!! include params/project.md !!!

</def-list>

### Query

<def-list>

!!! include query/fields.md !!!
!!! include query/meta.md !!!

</def-list>

### Returns

Returns the [user object](#the-user-object) for the currently authenticated user.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /:project/users/me
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "id": 1,
    "status": "active",
    "role": 1,
    "first_name": "Admin",
    "last_name": "User",
    "email": "admin@example.com",
    "token": "admin",
    "last_access_on": "2020-01-13T21:58:56+00:00",
    "last_page": "/my-project/settings/collections/a",
    "external_id": null,
    "theme": "auto",
    "2fa_secret": null,
    "password_reset_token": null,
    "timezone": "America/New_York",
    "locale": "en-US",
    "locale_options": null,
    "avatar": null,
    "company": null,
    "title": null,
    "email_notifications": true
  }
}
```

</info-box>
</div>
</template>
</two-up>

---

## Create a User

<two-up>
<template slot="left">

Create a new user.

### Parameters

<def-list>

!!! include params/project.md !!!

</def-list>

### Attributes

<def-list>

#### status <def-type alert>required</def-type>
One of `active`, `invited`, `draft`, `suspended`, `deleted`.

#### role <def-type alert>required</def-type>
Unique identifier of the role of this user.

#### first_name <def-type alert>required</def-type>
First name of the user.

#### last_name <def-type alert>required</def-type>
Last name of the user.

#### email <def-type alert>required</def-type>
Unique email address for the user.

#### password <def-type alert>required</def-type>
Password for the new user.

#### last_access_on <def-type>optional</def-type>
When this user logged in last.

#### last_page <def-type>optional</def-type>
Last page that the user was on.

#### external_id <def-type>optional</def-type>
ID used for SCIM.

#### theme <def-type>optional</def-type>
What theme the user is using. One of `light`, `dark`, or `auto`.

#### 2fa_secret <def-type>optional</def-type>
The 2FA secret string that's used to generate one time passwords.

#### password_reset_token <def-type>optional</def-type>
IF the users requests a password reset, this token will be sent in an email.

#### timezone <def-type>optional</def-type>
The user's timezone.

#### locale <def-type>optional</def-type>
The user's locale used in Directus.

#### locale_options <def-type>optional</def-type>
Not currently used. Can be used in the future to allow language overrides like different date formats for locales.

#### avatar <def-type>optional object</def-type>
The user's avatar.

#### company <def-type>optional</def-type>
The user's company.

#### title <def-type>optional</def-type>
The user's title.

#### email_notifications <def-type>optional</def-type>
Whether or not the user wants to receive notifications per email.

</def-list>

### Query

<def-list>

!!! include query/meta.md !!!

</def-list>

### Returns

Returns the [user object](#the-user-object) for the user that was just created.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
  POST /:project/users
```

</info-box>

<info-box title="Request">

```json
{
  "first_name": "Ben",
  "last_name": "Haynes",
  "email": "demo@example.com",
  "password": "d1r3ctu5",
  "role": 3,
  "status": "active"
}
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "id": 14,
    "status": "active",
    "role": 3,
    "first_name": "Ben",
    "last_name": "Haynes",
    "email": "demo@example.com",
    "token": null,
    "last_access_on": null,
    "last_page": null,
    "external_id": "53eece97-d84c-4940-9a6d-be2d4db07dc3",
    "theme": "auto",
    "2fa_secret": null,
    "password_reset_token": null,
    "timezone": "America/New_York",
    "locale": null,
    "locale_options": null,
    "avatar": null,
    "company": null,
    "title": null,
    "email_notifications": true
  }
}
```

</info-box>
</div>
</template>
</two-up>

---

## Update a User

<two-up>
<template slot="left">

Update an existing user

### Parameters

<def-list>

!!! include params/project.md !!!
!!! include params/id.md !!!

</def-list>

### Attributes

<def-list>

#### status <def-type>optional</def-type>
One of `active`, `invited`, `draft`, `suspended`, `deleted`.

#### role <def-type>optional</def-type>
Unique identifier of the role of this user.

#### first_name <def-type>optional</def-type>
First name of the user.

#### last_name <def-type>optional</def-type>
First name of the user.

#### email <def-type>optional</def-type>
Unique email address for the user.

#### password <def-type>optional</def-type>
Password for the new user.

#### last_access_on <def-type>optional</def-type>
When this user logged in last.

#### last_page <def-type>optional</def-type>
Last page that the user was on.

#### external_id <def-type>optional</def-type>
ID used for SCIM.

#### theme <def-type>optional</def-type>
What theme the user is using. One of `light`, `dark`, or `auto`.

#### 2fa_secret <def-type>optional</def-type>
The 2FA secret string that's used to generate one time passwords.

#### password_reset_token <def-type>optional</def-type>
IF the users requests a password reset, this token will be sent in an email.

#### timezone <def-type>optional</def-type>
The user's timezone.

#### locale <def-type>optional</def-type>
The user's locale used in Directus.

#### locale_options <def-type>optional</def-type>
Not currently used. Can be used in the future to allow language overrides like different date formats for locales.

#### avatar <def-type>optional object</def-type>
The user's avatar.

#### company <def-type>optional</def-type>
The user's company.

#### title <def-type>optional</def-type>
The user's title.

#### email_notifications <def-type>optional</def-type>
Whether or not the user wants to receive notifications per email.

</def-list>

### Query

<def-list>

!!! include query/fields.md !!!
!!! include query/meta.md !!!

</def-list>

### Returns

Returns the [user object](#the-user-object) for the user that was just updated.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
 PATCH /:project/users/:id
```

</info-box>

<info-box title="Request">

```json
{
  "status": "suspended"
}
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "id": 14,
    "status": "suspended",
    "role": 3,
    "first_name": null,
    "last_name": null,
    "email": "demo@example.com",
    "token": null,
    "last_access_on": null,
    "last_page": null,
    "external_id": "53eece97-d84c-4940-9a6d-be2d4db07dc3",
    "theme": "auto",
    "2fa_secret": null,
    "password_reset_token": null,
    "timezone": "America/New_York",
    "locale": null,
    "locale_options": null,
    "avatar": null,
    "company": null,
    "title": null,
    "email_notifications": true
  }
}
```

</info-box>
</div>
</template>
</two-up>

---

## Delete a User

<two-up>
<template slot="left">

Delete an existing user

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
DELETE /:project/users/:id
```

</info-box>
</div>
</template>
</two-up>

---

## Invite a New User

<two-up>
<template slot="left">

Invites one or more users to this project. It creates a user with an invited status, and then sends an email to the user with instructions on how to activate their account.

### Parameters

<def-list>

!!! include params/project.md !!!

</def-list>

### Attributes

<def-list>

#### email <def-type alert>required</def-type>
Email address or array of email addresses of the to-be-invited user(s).

</def-list>

### Query

No query parameters available.

### Returns

The newly created [user object](#the-user-object).

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
  POST /:project/users/invite
```

</info-box>

<info-box title="Request">

```json
{
  "email": "demo@example.com"
}
```

</info-box>

<info-box title="Response">

```json
{
  "data": [
    {
      "id": 18,
      "status": "invited",
      "role": null,
      "first_name": null,
      "last_name": null,
      "email": "demo@example.com",
      "token": null,
      "last_access_on": null,
      "last_page": null,
      "external_id": "fba6f42d-cc99-4f6a-a620-51000001f355",
      "theme": "auto",
      "2fa_secret": null,
      "password_reset_token": null,
      "timezone": "America/New_York",
      "locale": null,
      "locale_options": null,
      "avatar": null,
      "company": null,
      "title": null,
      "email_notifications": true
    }
  ]
}
```

</info-box>
</div>
</template>
</two-up>

---

## Accept User Invite

<two-up>
<template slot="left">

Accepts and enables an invited user using a JWT invitation token.

### Parameters

<def-list>

!!! include params/project.md !!!

#### token <def-type alert>required</def-type> <def-type>jwt</def-type>
JWT token that was sent in the email.

</def-list>

### Attributes

No attributes available.

### Query

No query parameters available.

### Returns

The activated [user object](#the-user-object).

</def-list>

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
  POST /:project/users/invite/:token
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "id": 20,
    "status": "active",
    "role": null,
    "first_name": null,
    "last_name": null,
    "email": "demo@example.com",
    "token": null,
    "last_access_on": null,
    "last_page": null,
    "external_id": "389ffc3e-359c-4ee1-b301-14ba10b36ef4",
    "theme": "auto",
    "2fa_secret": null,
    "password_reset_token": null,
    "timezone": "America/New_York",
    "locale": null,
    "locale_options": null,
    "avatar": null,
    "company": null,
    "title": null,
    "email_notifications": true
  }
}
```

</info-box>
</div>
</template>
</two-up>

---

## Track the Last Used Page

<two-up>
<template slot="left">

Updates the last used page field of the user. This is used internally to be able to open the Directus admin app from the last page you used.

### Parameters

<def-list>

!!! include params/project.md !!!
!!! include params/id.md !!!

</def-list>

### Attributes

<def-list>

#### last_page <def-type alert>required</def-type>
Path of the page you used last.

</def-list>

### Query

No query parameters available.

### Returns

Returns the [user object](#the-user-object) of the user that was just updated.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
 PATCH /:project/users/:id/tracking/page
```

</info-box>

<info-box title="Request">

```json
{
  "last_page": "/thumper/settings/"
}
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "id": 15,
    "status": "active",
    "role": 3,
    "first_name": null,
    "last_name": null,
    "email": "sdf@example.com",
    "token": null,
    "last_access_on": "2020-01-13T22:31:43+00:00",
    "last_page": "/thumper/setting/",
    "external_id": "f8e90dec-b6ec-4149-b1f1-c6717b24d70c",
    "theme": "auto",
    "2fa_secret": null,
    "password_reset_token": null,
    "timezone": "America/New_York",
    "locale": null,
    "locale_options": null,
    "avatar": null,
    "company": null,
    "title": null,
    "email_notifications": true
  }
}
```

</info-box>
</div>
</template>
</two-up>

---

## List User Revisions

<two-up>
<template slot="left">

List the revisions made to the given user.

### Parameters

<def-list>

!!! include params/project.md !!!
!!! include params/id.md !!!

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

Returns an array of [revision](/api/revisions.html#the-revision-object) objects.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /:project/users/:id/revisions
```

</info-box>
<info-box title="Response">

```json
{
  "data": [
    {
      "id": 35,
      "activity": 37,
      "collection": "directus_users",
      "item": "15",
      "data": {
        "id": "15",
        "status": "active",
        "role": "3",
        "first_name": null,
        "last_name": null,
        "email": "sdf@example.com",
        "token": null,
        "last_access_on": null,
        "last_page": null,
        "external_id": "f8e90dec-b6ec-4149-b1f1-c6717b24d70c",
        "theme": "auto",
        "2fa_secret": null,
        "password_reset_token": null,
        "timezone": "America/New_York",
        "locale": null,
        "locale_options": null,
        "avatar": null,
        "company": null,
        "title": null,
        "email_notifications": true
      },
      "delta": {
        "status": "active"
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

## Retrieve a User Revision

<two-up>
<template slot="left">

Retrieve a single revision of the user by offset.

### Parameters

<def-list>

!!! include params/project.md !!!

#### offset <def-type alert>required</def-type>
How many revisions to go back in time.

</def-list>

### Query

<def-list>

!!! include query/fields.md !!!
!!! include query/meta.md !!!

</def-list>

### Returns

Returns the [revision object](/api/revisions.html#the-revision-object) for the given unique identifier.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /:project/users/:id/revisions/:offset
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "id": 39,
    "activity": 44,
    "collection": "directus_users",
    "item": "15",
    "data": {
      "id": "15",
      "status": "active",
      "role": "3",
      "first_name": null,
      "last_name": null,
      "email": "sdf@example.com",
      "token": null,
      "last_access_on": "2020-01-13 22:31:43",
      "last_page": "/thumper/setting/",
      "external_id": "f8e90dec-b6ec-4149-b1f1-c6717b24d70c",
      "theme": "auto",
      "2fa_secret": null,
      "password_reset_token": null,
      "timezone": "America/New_York",
      "locale": null,
      "locale_options": null,
      "avatar": null,
      "company": null,
      "title": null,
      "email_notifications": true
    },
    "delta": [],
    "parent_collection": null,
    "parent_item": null,
    "parent_changed": false
  }
}
```

</info-box>
</div>
</template>
</two-up>

---
