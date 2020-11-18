## Persisting the configuration / authentication

#### Supported stores
Every store is supported as long as the storage instance exposes the following API:

```ts
interface IStorageAPI {
  getItem<T extends any = any>(key: string): T;
  setItem(key: string, value: any): void;
  removeItem(key: string): void;
}
```

this means that the SDK supports [localStorage]() and [sessionStorage]() out of the box!

#### Persisting asynchronus via e.g. a service
*This feature is not supported at the moment.*
