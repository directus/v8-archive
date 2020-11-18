## `@directus/sdk-js` with RXJS

The SDK uses Promises across the board, making integration with RXJS painless:

```ts
import { from } from 'rxjs';
import SDK from '@directus/sdk-js';

const client = new DirectusSDK({
    url: "https://demo-api.directus.app/",
    email: "admin@example.com",
    password: "password"
});

// Create an Observable out of a promise
const data = from(client.getItems('movies'));

// Subscribe to begin listening for async result
data.subscribe({
 next(response) { console.log(response); },
 error(err) { console.error('Error: ' + err); },
 complete() { console.log('Completed'); }
});
```
