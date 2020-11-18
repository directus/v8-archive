---
pageClass: page-reference
---

# Authentication

<two-up>

<template slot="left">

By default, all data in the system is off limits for unauthenticated users. To gain access to protected data, you must include an access token with every request.

</template>

<info-box slot="right" title="Endpoints">

```endpoints
  POST /:project/auth/authenticate
  POST /:project/auth/refresh
  POST /:project/auth/password/request
  POST /:project/auth/password/reset
   GET /:project/auth/sso
   GET /:project/auth/sso/:provider
   GET /:project/auth/sso/:provider/callback
```

</info-box>
</two-up>

---

## Tokens

<two-up>
<template slot="left">

Tokens can be passed in one of three ways:

### Authorization Header

```
Authorization: bearer <token>
```

By default, apache servers strip the Authentication header from requests, and blocks directus from seeing it. Adding the option 'CGIPassAuth On' to the directory statement in the site configuration in apache will resolve this.
```
<Directory /var/www/directus/public/>
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
    CGIPassAuth On
</Directory>
```

### Query Parameter

```
?access_token=<token>
```

### Cookie

When authenticating through the `/auth/authenticate` endpoint, you can instruct the API to set a cookie instead of returning the token as a string. This is the most secure option for building web-based applications, as this cookie can't be read from client side JavaScript. This cookie will also automatically refresh as long as you use it.

```
Cookie: directus-<project>-session=<token>
```

---

There are two types of tokens:

### Temporary (JWT)

These tokens are generated through the `/auth/authenticate` endpoint (below) and have a lifespan of 20 minutes. These tokens can be refreshed using the `/auth/refresh` endpoint.

### Static token

Each user can have one static token that will never expire. This is useful for server-to-server communication, but is also less secure than the JWT token. Static tokens can only be set through the database directly in the `directus_users.token` column.

</template>

<template slot="right">

::: tip
See the Authenticate endpoint down below to learn how to retrieve a token.
:::

</template>
</two-up>

---

## Retrieve a Temporary Access Token

<two-up>
<template slot="left">

### Parameters

<def-list>

!!! include params/project.md !!!

</def-list>

### Attributes

<def-list>

#### email <def-type alert>required</def-type>
Email address of the user you're retrieving the access token for.

#### password <def-type alert>required</def-type>
Password of the user.

#### mode <def-type>optional</def-type>
Choose between retrieving the token as a string, or setting it as a cookie. One of `jwt`, `cookie`. Defaults to `jwt`.

#### otp <def-type>optional</def-type>
If 2FA is enabled, you need to pass the one time password.

</def-list>

### Query

<def-list>

No query parameters available.

</def-list>

### Returns

Returns the token (if `jwt` mode is used) and the user record for the user you just authenticated as.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
  POST /:project/auth/authenticate
```

</info-box>

<info-box title="Request">

```json
{
  "email": "admin@example.com",
  "password": "password"
}
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "token": "eyJ0eXAiOi...",
    "user": {
      "id": "1",
      "status": "active",
      "role": "1",
      "first_name": "Admin",
      "last_name": "User",
      "email": "admin@example.com",
      "timezone": "America/New_York",
      "locale": "en-US",
      "locale_options": null,
      "avatar": null,
      "company": null,
      "title": null,
      "external_id": null,
      "theme": "auto",
      "2fa_secret": null,
      "password_reset_token": null
    }
  },
  "public": true
}
```

</info-box>
</div>
</template>
</two-up>

---

## Refresh a Temporary Access Token

<two-up>
<template slot="left">

::: tip Cookie mode
You don't have to use this is you're using cookies for authentication.
:::

### Parameters

<def-list>

!!! include params/project.md !!!

</def-list>

### Attributes

<def-list>

#### token <def-type alert>required</def-type>
JWT access token you want to refresh. This token can't be expired.

</def-list>

### Query

<def-list>

No query parameters available.

</def-list>

### Returns

Returns the new token.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
  POST /:project/auth/refresh
```

</info-box>

<info-box title="Request">

```json
{
  "token": "eyJ0eXAiOiJKV..."
}
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "token": "eyJ0eXAiOiJ..."
  },
  "public": true
}
```

</info-box>
</div>
</template>
</two-up>

---

## Request a Password Reset

<two-up>
<template slot="left">

Request a reset password email to be send.

### Parameters

<def-list>

!!! include params/project.md !!!

</def-list>

### Attributes

<def-list>

#### email <def-type alert>required</def-type>
Email address of the user you're requesting a reset for.

#### reset_url <def-type>optional</def-type>
Provide a custom reset url which the link in the Email will lead to. The reset token will be passed as a parameter.

</def-list>

### Query

No query parameters available.

### Returns

Sends the email. No data is returned.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
  POST /:project/auth/password/request
```

</info-box>

<info-box title="Request">

```json
{
  "email": "admin@example.com",
  "reset_url": "https://mydomain/passwordreset"
}
```

</info-box>

</div>
</template>
</two-up>

---

## Reset a Password

<two-up>
<template slot="left">

The request a password reset endpoint sends an email with a link to the admin app which in turn uses this endpoint to allow the user to reset their password.

### Parameters

<def-list>

!!! include params/project.md !!!

</def-list>

### Attributes

<def-list>

#### password <def-type alert>required</def-type>
New password for the user.

#### token <def-type alert>required</def-type>
One-time use JWT token that is used to verify the user.

</def-list>

### Query

No query parameters available.

### Returns

Resets the password. No data is returned.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
  POST /:project/auth/password/reset
```

</info-box>

<info-box title="Request">

```json
{
  "token": "eyJ0eXAiOiJKV1Qi...",
  "password": "test"
}
```

</info-box>

</div>
</template>
</two-up>

---

## List the SSO Providers

<two-up>
<template slot="left">

List the SSO providers.

### Parameters

<def-list>

!!! include params/project.md !!!

</def-list>

### Query

No query parameters available.

### Returns

Returns an array of active SSO provider names.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /:project/auth/sso
```

</info-box>
<info-box title="Response">

```json
{
  "data": ["github", "facebook"],
  "public": true
}
```

</info-box>
</div>
</template>
</two-up>

---

## Open SSO Provider

<two-up>
<template slot="left">

Opens the provided SSO provider's login page.

### Parameters

<def-list>

!!! include params/project.md !!!

#### provider <def-type alert>required</def-type>
Key of the activated SSO provider.

</def-list>

### Query

<def-list>

#### mode <def-type alert>required</def-type>
Controls if the API sets a cookie or returns a JWT on successful login. One of `jwt`, `cookie`

#### redirect_url <def-type alert>required</def-type>
Where to redirect on successful login.

</def-list>

### Returns

Opens the provider's login page.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /:project/auth/sso/:provider
```

</info-box>
</div>
</template>
</two-up>

---

## SSO Callback

<two-up>
<template slot="left">

This shouldn't be called by your project directly. This is the URL configured in your SSO provider to redirect to on successful login.

### Parameters

<def-list>

!!! include params/project.md !!!

#### provider <def-type alert>required</def-type>
Key of the activated SSO provider.

</def-list>

### Query

Relies on the SSO provider to pass the correct query parameters.

### Returns

The token if `jwt` mode is used, or sets a cookie and redirects to `redirect_url` from the _Open SSO Provider_ request if `cookie` mode is used.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /:project/auth/sso/:provider/callback
```

</info-box>
</div>
</template>
</two-up>

---
