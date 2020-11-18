---
pageClass: page-reference
---

# Server

<two-up>

::: slot left
Access to where Directus runs. Allows you to make sure your server has everything needed to run the platform, and check what kind of latency we're dealing with.
:::

<info-box title="Endpoints" slot="right">

```endpoints
   GET /server/info
   GET /server/ping
```

</info-box>
</two-up>

---

## Retrieve Server Info


<two-up>
<template slot="left">

Perform a system status check and return the options.

### Parameters

No URL parameters available.

### Query

<def-list>

#### super_admin_token <def-type alert>required</def-type>
The first time you create a project, the provided token will be saved and required for subsequent project installs. It can also be found and configured in `/config/__api.json` on your server.

</def-list>

### Returns

Lists if you have all the requirements needed for Directus and shows some other useful information.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /server/info
```

</info-box>
<info-box title="Response">

```json
{
  "data": {
    "directus": "8.3.1",
    "server": {
      "type": "Apache/2.4.25 (Debian)",
      "rewrites": null,
      "os": "Linux",
      "os_version": "#1 SMP Tue Jul 2 22:58:16 UTC 2019"
    },
    "php": {
      "version": "7.2.22-1+0~20190902.26+debian9~1.gbpd64eb7",
      "max_upload_size": 104857600,
      "extensions": {
        "pdo": true,
        "mysqli": true,
        "curl": true,
        "gd": true,
        "fileinfo": true,
        "mbstring": true,
        "json": true
      }
    },
    "permissions": {
      "public": "0755",
      "logs": "0755",
      "uploads": "0755"
    }
  }
}
```

</info-box>
</div>
</template>
</two-up>

---

## Ping the server


<two-up>
<template slot="left">

Ping, pong. Ping.. pong. 🏓

### Parameters

No URL parameters available.

### Query

No query parameters available.

### Returns

`pong`

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /server/ping
```

</info-box>
<info-box title="Response">

```text
pong
```

</info-box>
</div>
</template>
</two-up>