---
pageClass: page-reference
---

# Mail

<two-up>
<template slot="left">

Send electronic mail through the electronic post.

::: tip
If the email doesn't send, make sure to check your email configuration. Using a separate SMTP server is recommended.
:::

</template>
<info-box title="Endpoints" slot="right">

```endpoints
  POST /:project/mail
```

</info-box>
</two-up>

---

## Send an Email

<two-up>
<template slot="left">

Send an email

### Parameters

<def-list>

!!! include params/project.md !!!

</def-list>

### Attributes

<def-list>

#### to <def-type alert>required</def-type>
User ID, email address, or object in the format `{ email, name }`. You can send an email to multiple people at the same time by passing an array here.

#### body <def-type alert>required</def-type>
Body of the email.

#### subject <def-type>optional</def-type>
Email subject.

#### type <def-type>optional</def-type>
HTML or plain text

#### data <def-type>optional</def-type>
Key value pairs of variables that can be used in the body.

</def-list>

### Returns

Returns an empty body with HTTP status code 204.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
  POST /:project/mail
```

</info-box>

<info-box title="Request">

```json
{
  "to": [
    "user@example.com",
    "admin@example.com"
  ],
  "subject": "New Password",
  "body": "Hello <b>{{name}}</b>, this is your new password: {{password}}.",
  "type": "html",
  "data": {
    "name": "John Doe",
    "password": "secret"
  }
}
```

</info-box>
</div>
</template>
</two-up>
