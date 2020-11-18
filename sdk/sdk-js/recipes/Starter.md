## Getting started with the SDK

### Authentication

#### Login 

##### Via token
```ts
import SDK from '@directus/sdk-js';

const sdk = new SDK({
    url: 'https://my-directus.com',
    token: 'my-token-comes-here'
});
```

##### Via User
```ts
import SDK from '@directus/sdk-js';

const sdk = new SDK({
    url: 'https://my-directus.com',
    email: 'admin@my-directus.com',
    password: 'my-password'
});
```

#### Logout
```ts
import SDK from '@directus/sdk-js';

const sdk = new SDK({
    url: 'https://my-directus.com',
    token: 'my-token-comes-here'
});

sdk.logout();
```

#### Setting token, localExp, tokenExpirationTime etc. later on
It is possible to modify the shared configuration during the runtime of your program.
To do so just edit the `SDK.config` fields, which will change the setting for your SDK instance.

```ts
import SDK, { Configuration } from '@directus/sdk-js';

Configuration.defaults = {
    project: "default-project",
    tokenExpirationTime: 10 * 6 * 1000; // 10s instead of 5s
};

const sdk1 = new SDK({ url: 'https://my-directus.com' });
const sdk2 = new SDK({ url: 'https://my-directus.com' });
```
