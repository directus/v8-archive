# SDK JS

> A lightweight JavaScript library that makes working with the Directus API even easier. It keeps you logged in, handles token management, and provides quick access to all [API Endpoints](../api/reference.md).

## Installation

You can install the SDK from `npm` by running:

```bash
npm install @directus/sdk-js
```

```js
import DirectusSDK from "@directus/sdk-js";

const client = new DirectusSDK();
```

Alternatively, you can use the bundle hosted on a static npm cdn:

```html
<script src="https://unpkg.com/@directus/sdk-js@5.3.4/dist/directus-sdk.umd.min.js"></script>

<script>
  const client = new DirectusSDK();
</script>
```

## Usage

### Authentication

You can connect and login to the API in a few ways:

**Within a Directus Extension**

If you're making API requests from a custom VueJS Directus extension, you can use the credentials the user already provided Directus on initial login.

```js
import DirectusSDK from "@directus/sdk-js";

const client = new DirectusSDK({
  url: "https://api.directus.cloud/",
  project: "dcABCDEF123456",
  storage: window.localStorage
});
```

However, Directus provides each extension VueJS component with `this.$api`, an instance of `DirectusSDK` already logged in. Read more at https://docs.directus.io/advanced/app/sdk-api.html.

```js
export default {
  name: "example-component",
  created() {
    // No login needed
    this.$api.getItems("projects")
      .then(/* do something with response */)
  }
}
```

**Using JWT**

If you're making API requests from your own client side JS, you should login using the asynchronous `login` method. The SDK will fetch an access token based on the credentials and use that for all subsequent requests.

```js{3,5-10}
import DirectusSDK from "@directus/sdk-js";

const client = new DirectusSDK();

// This returns a promise - don't try to access any data until this promise resolves
client.login({
  url: "https://api.directus.cloud/",
  project: "dcABCdefHIJklm",
  email: "admin@example.com",
  password: "password"
});
```

#### Staying logged in

The SDK will automatically refresh the token until you call the `.logout()` method.

You can provide a storage method to persist the token across refreshes and browser re-opens:

```js{6}
import DirectusSDK from "@directus/sdk-js";

const client = new DirectusSDK({
  url: "https://api.directus.cloud/",
  project: "dcABCdefHIJklm",
  storage: window.localStorage
});
```

This storage can be any provider, as long as it has synchronous `.getItem()`, `.setItem()` and `.removeItem()` methods. `window.localStorage` and `window.sessionStorage` work natively in the browser.

**Using a static access token**
Alternatively, you can connect to the API with a static token (as controlled by the `token` field in the directus_users collection). This token doesn't expire and thus shouldn't be used on the client side.

```js{3-7}
import DirectusSDK from "@directus/sdk-js";

const client = new DirectusSDK({
  url: "https://api.directus.cloud/",
  project: "dcABCdefHIJklm",
  token: "demo"
});
```

:::tip
If you're using solely routes and data with public access, you don't have to log in.
:::

### Requests

All methods return a promise that either resolves with the requested data, or rejects with the reason why.

```js
client.getItems("movies")
  .then(data => {
    // Do something with the data
  })
  .catch(error => console.error(error));
```

This also means that you can use the SDK with `async/await`:

```js
async function fetchAllItems() {
  const data = await client.getItems("movies");
  return data;
}
```

## Reference

Note that all of the following methods are asynchronous and return promises, unless explicitely noted.

If you think a method is missing, please reach out on GitHub or [Slack](https://directus.chat)!

### Asset

#### `getAssetUrl(private_hash, params?)`

Return the URL of an asset's original or thumbnail.

See [API Assets (Thumbnails)](../api/assets.md) for the structure of the `params` object.
If `params` is empty or `undefined`, the URL of the original will be returned.

:::tip
This method is synchronous.
:::

#### `getAsset(private_hash, params?)`

Return an asset's original or its thumbnail as an ArrayBuffer.

See [API Assets (Thumbnails)](../api/assets.md) for the structure of the `params` object.
If `params` is empty or `undefined`, the URL of the original will be returned.


### Authentication

#### `login(credentials)`

Login to the API

The credentials object has the following structure:

```js
{
  email: "admin@example.com",
  password: "password",

  // Optional:
  url: "https://api.directus.cloud/",
  project: "dcABCdefHIJklm", // Defaults to '_'
  persist: true // Defaults to true
}
```

:::warning
Make sure to provide the `url` of the API you're trying to log in to before running the `login()` method if you don't provide the url in the `credentials` object.
:::

Login to the demo API without keeping the user logged in

```js
client.login({
  email: "admin@example.com",
  password: "password",
  persist: false
});
```

Login to a previously defined API URL

```js
client.url = "https://api.directus.cloud/"

client.login({
  email: "admin@example.com",
  password: "password"
});
```

The client options accepts the `mode` flag that lets you toggle between JWT and Cookies for authentication. 

```js
import DirectusSDK from "@directus/sdk-js";

const client = new DirectusSDK({
  url: "https://api.directus.cloud/",
  project: "dcABCdefHIJklm",
  mode: "cookie"
});

client.login({
  email: "admin@example.com",
  password: "password"
});
```

:::tip
You don't have to provide a persist storage adapter when using cookies. The API will manage the cookie and its expiration.
:::

---

#### `logout()`

Make the SDK forget the token, project, and URL of the API.

```js
client.logout();
```

---

#### `refresh(token)`

Will fetch a new access token based on the provided token.

Refresh a token stored in sessionStorage with a new token.

```js
const savedToken = window.sessionStorage.getItem("token");

client.refresh(savedToken)
  .then(({ token }) => {
    window.sessionStorage.setItem("token", token);
  });
```

:::tip
If you use the `login()` method, you most likely don't need to touch this one as it's being managed for you.
:::

---

#### `refreshIfNeeded()`

Checks if the currently used token is about to expire (< 30s until expiry) and will fetch a new one if that's the case.


```js
client.refreshIfNeeded();
```

:::tip
If you use the `login()` method, you most likely don't need to touch this one as it's being managed for you.
:::

---

#### `requestPasswordReset(email)`

Request a reset-password email based on the given email

```js
client.requestPasswordReset("admin@example.com");
```

---

### Activity

#### `getActivity(params = {})`

Get the items from directus_activity.

Get the latest activity sorted by date

```js
client.getActivity({
  sort: "action_on"
});
```

---

### Bookmarks

#### `getMyBookmarks(params = {})`

Get the bookmarks of the currently logged in user

Get the user's bookmarks

```js
client.getMyBookmarks();
```

---

### Collections

#### `getCollections(params = {})`

Get all available collections

```js
client.getCollections();
```

---

#### `getCollection(collection, params = {})`

Get a single collection's info by name

```js
client.getCollection("movies");
```

---

#### `createCollection(data = {})`

Create a new collection

```js
client.createCollection({
  collection: "projects",
  note: "This is a new collection",
  fields: [{
    field: "id",
    type: "integer",
    datatype: "int",
    interface: "primary_key",
    primary_key: true,
    auto_increment: true,
    length: 10,
    signed: false
  }]
});
```

---

#### `updateCollection(collection, data = {})`

Update a collection's info by name

```js
client.updateCollection("projects", {
  icon: "person"
});
```

---

#### `deleteCollection(collection)`

Delete a collection

```js
client.deleteCollection("projects");
```

:::danger
This will remove the entire collection including everything in it without confirmation of safety fallbacks.
:::

---

### Collection Presets

#### `createCollectionPreset(data)`

Creates a new collection preset (eg bookmark / listing preferences)

```js
client.createCollectionPreset({
  title: "My First Bookmark",
  view_query: {
    tabular: {
      fields: ["status", "datetime", "sale_amount", "tax_amount", "member"],
      sort: "-datetime"
    }
  }
});
```

---

#### `updateCollectionPreset(primaryKey, data)`

Update a collection preset by primary key

```js
client.updateCollectionPreset(15, {
  title: "My New Bookmark Title"
});
```

---

#### `deleteCollectionPreset()`

Delete a collection preset by primary key

```js
client.deleteCollectionPreset(15);
```

---

### Extensions

#### `getInterfaces()`

Get all the available interfaces

```js
client.getInterfaces();
```

---

#### `getLayouts()`

Get all the available layouts

```js
client.getLayouts();
```

---

#### `getModules()`

Get all the available modules

```js
client.getModules();
```

---

### Fields

#### `getAllFields(params = {})`

Get all fields of all collections

```js
client.getAllFields();
```

---

#### `getFields(collection, params = {})`

The the fields of a given collection

```js
client.getFields("movies");
```

---

#### `getField(collection, fieldName, params = {})`

Get the field information for a single given field

```js
client.getField("movies", "actors");
```

---

#### `createField(collection, fieldInfo)`

Create a new field in a given collection

```js
client.createField("movies", {
  field: "runtime",
  type: "integer",
  datatype: "int",
  interface: "numeric"
});
```

---

#### `updateField(collection, fieldName, fieldInfo)`

Update a given field

```js
client.updateField("movies", "runtime", { length: 10 });
```

---

#### `updateFields(collection, fieldsInfoOrFieldNames, fieldInfo = null)`

Update multiple fields at once

```js
// Set multiple fields to the same value
client.updateFields(
  "projects",
  ["first_name", "last_name", "email"],
  { default_value: "" }
);

// Set multiple fields to different values
client.updateFields("projects", [
  { id: 14, sort: 1 },
  { id: 17, sort: 2 },
  { id: 22, sort: 3 }
]);
```

---

#### `deleteField(collection, fieldName)`

Delete a field by name

```js
client.deleteField("movies", "runtime");
```

:::danger
This will remove the field from the collection including all the saved data.
:::

---

### Files

#### `uploadFiles(data, onUploadProgress)`

Will upload files in multipart/form-data enctype to the API. `data` is a JS FormData containing the files to upload.

```html
<form id="my-form">
  <input type="file" name="file" />
</form>

<script>
const form = document.querySelector("#my-form");
const data = new FormData(form);

client.uploadFiles(data, onUploadProgress);

function onUploadProgress(progressEvent) {
  const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);

  console.log(percentCompleted + "% Done");
}
</script>
```

---

### Items

#### `updateItem(collection, primaryKey, data)`

Update an item

```js
client.updateItem("movies", 15, {
  title: "A newer hope"
});
```

---

#### `updateItems(collection, data)`

Update multiple items at once

```js
client.updateItems("movies", [
  { id: 15, sort: 1 },
  { id: 21, sort: 2 },
  { id: 11, sort: 3 }
]);
```

---

#### `createItem(collection, body)`

Create a new item

```js
client.createItem("movies", {
  title: "The DB of Destiny",
  runtime: 210,
  rating: 2.4
});
```

---

#### `createItems(collection, body)`

Create multiple items

```js
client.createItems("movies", [
  {
    title: "The DB of Destiny",
    runtime: 210,
    rating: 2.4
  },
  {
    title: "Postgresalypto",
    runtime: 190,
    rating: 4.3
  }
]);
```

---

#### `getItems(collection, params = {})`

Get items from a given collection

```js
client.getItems("movies", {
  filter: {
    runtime: {
      gt: 200
    }
  }
});
```

---

#### `deleteItem(collection, primaryKey)`

Delete an item

```js
client.deleteItem("movies", 15);
```

:::danger
This doesn't perform any checks. Make sure you don't have any relations relying on this item before deleting it.
:::

---

#### `deleteItems(collection, primaryKeys)`

Delete multiple items

```js
client.deleteItems("movies", [15, 21, 35]);
```

:::danger
This doesn't perform any checks. Make sure you don't have any relations relying on these items before deleting them.
:::

---

### Listing Preferences

#### `getMyListingPreferences(collection, params = {})`

Get the listing prefernces of the current user. These are used in the Directus app to render the items in a certain way. (Eg tabular vs cards)

---

### Permissions

#### `getPermissions(params = {})`

Get all permissions in the system (raw records).

This will retrieve all permissions for all users.

```js
client.getPermissions();
```

---

#### `getMyPermissions(params = {})`

The the permissions that apply to the current user.

```js
client.getMyPermissions();
```

---

#### `createPermissions(data)`

Create multiple new permissions.

```js
client.createPermissions([
  {
    collection: "movies",
    role: 3,
    status: "released",
    create: "full",
    read: "mine",
    update: "role",
    delete: "none"
  },
  {
    collection: "movies",
    role: 2,
    status: "released",
    create: "none",
    read: "none",
    update: "none",
    delete: "full"
  }
]);
```

---

#### `updatePermissions(data)`

Update multiple permissions

```js
client.updatePermissions([
  {
    id: 15,
    delete: "none"
  },
  {
    id: 21,
    update: "full"
  }
]);
```

---

### Relations

#### `getRelations(params = {})`

Get all the Directus relations.

```js
client.getRelations();
```

---

#### `createRelation(data)`

Create a new relationship.

```js
client.createRelation({
  collection_many: "sales",
  field_many: "ticket",
  collection_one: "tickets"
});
```

---

#### `updateRelation(primaryKey, data)`

Update a relationship by primary key.

```js
client.updateRelation(22, {
  field_one: "sold_tickets"
});
```

---

#### `getCollectionRelations(collection, params = {})`

Get the relationships that apply to a given collection.

```js
client.getCollectionRelations("movies");
```

---

### Revisions

#### `getItemRevisions(collection, primaryKey, params = {})`

Get all the revisions of a single item.

```js
client.getItemRevisions("movies", 15);
```

---

#### `revert(collection, primaryKey, revisionID)`

Revert an item to a previous state based on the ID of the revision.

```js
client.revert("movies", 15, 21);
```

:::warning
This doesn't take schema changes into account. Use with caution.
:::

---

### Roles

#### `getRole(primaryKey, params = {})`

Get a user role by primary key.

```js
client.getRole(primaryKey, params = {})
```

---

#### `getRoles(params = {})`

Get all the user roles

```js
client.getRoles();
```

---

#### `updateRole(primaryKey, body)`

Update a user role

```js
client.updateRole(15, {
  name: "Interns"
});
```

---

#### `createRole(body)`

Create a new user role

```js
client.createRole({
  name: "Project Managers",
  ip_whitelist: ["192.168.0.1"]
});
```

---

#### `deleteRole(primaryKey)`

Delete a user role

```js
client.deleteRole(15);
```

:::danger
This doesn't affect the users in the role. Make sure to remove all users from this role before deleting the role. Otherwise your users will lose access to the system.
:::

---

### Settings

#### `getSettings(params = {})`

Get Directus' settings

```js
client.getSettings();
```

---

### Users

#### `getUsers(params = {})`

Get a list of available users in Directus

```js
client.getUsers({
  filter: {
    status: {
      eq: "active"
    }
  }
});
```

---

#### `getUser(primaryKey, params = {})`

Get information about a single user

```js
client.getUser(15, {
  fields: ["first_name", "last_name"]
});
```

---

#### `getMe(params = {})`

Get the currently logged in user.

```js
client.getMe();
```

---

#### `createUser(body)`

Create a single user

```js
client.createUser({
  first_name: "Ben",
  last_name: "Haynes",
  email: "demo@example.com",
  password: "d1r3ctu5",
  role: 3,
  status: "active"
});
```

---

#### `updateUser(primaryKey, body)`

Update a user by primary key

```js
client.updateUser(5, {
  locale: "nl-NL"
});
```

---

### Server Utils

#### `ping()`

See if the server is live and reachable

```js
client.ping();
```

---

#### `serverInfo()`

Fetches the info of the server, like php version or server type.

```js
client.serverInfo();
```

---

#### `getThirdPartyAuthProviders()`

Get all the third party auth providers that can be used to login to the API

```js
client.getThirdPartyAuthProviders();
```

---

### Internal methods

These methods are used by all other methods listed above and aren't generally used in normal operation. You can use the following methods to make requests to custom endpoints or use HTTP methods that aren't provided by the methods above. The methods down below are scoped in the `api` class.

#### `request(method, endpoint, params = {}, data = {}, noProject = false, headers = {})`

Make a generic request to the API based on the parameters provided.

Make a post request to `/update`:

```js
client.api.request("post", "/update");
```

Get the server info:

```js
client.api.request("get", "/server/info", {}, {}, true);
```

---

#### `get(endpoint, params = {})`

Send a `GET` request to a given endpoint.

Get all movies sorted by date:

```js
client.api.get("/items/movies", {
  sort: "-datetime"
});
```

---

#### `post(endpoint, body = {}, params = {})`

Send a `POST` request to a given endpoint.

Create a new movie only returning the ID of the newly created item

```js
client.api.post(
  "/items/movies",
  { title: "A New Hope" },
  { fields: "id" }
);
```

---

#### `patch(endpoint, body = {}, params = {})`

Send a `PATCH` request to a given endpoint.

Update the movie with ID 5

```js
client.api.patch("/items/movies/5", { title: "A Newer Hope" });
```

---

#### `put(endpoint, body = {}, params = {})`

Send a `PUT` request to a given endpoint.

_This method isn't used by the API by default_

---

#### `delete(endpoint)`

Send a `DELETE` request to a given endpoint.

Delete the movie with ID 5

```js
client.api.delete("/items/movies/5");
```

---

#### `startInterval(fireImmediately = false)`

Start the token refresh interval. This will check the tokens validity every 10 seconds and will fetch a new token when the current token is about to expire.

The `fireImmediately` method controls whether or not to immediately try refreshing the token.

```js
client.startInterval(true);
```

---

#### `stopInterval()`

Stops the refresh token interval

```js
client.stopInterval();
```
