## `@directus/sdk-js` with Vue

### Importing the SDK 

You can import the Directus SDK in any Vue single file component:

```vue
<template>
    <div id="app">
        <button @click="fetchMovies">Fetch Movies</button>

        <ul>
            <li v-for="movie in movies">{{ movie.name }}</li>
        </ul>
    </div>
</template>

<script>
import DirectusSDK from "@directus/sdk-js";

export default {
    name: "App",
    data() {
        return {
            movies: null
        };
    },
    created() {
        this.client = new DirectusSDK({
            url: "https://demo-api.directus.app"
        });
    },
    methods: {
        async fetchMovies() {
            const { data } = await this.client.getItems("movies");
            this.movies = data;
        }
    }
}
```

### Reusable client

When you want to re-use the same instance of the SDK across multiple components, we recommend using a separate file for the SDK which you can then import in every place you need the sdk, for example:

```js
// client.js
import DirectusSDK from "@directus/sdk-js";

const client = new DirectusSDK({
    url: "https://demo-api.directus.app"
});

export default client;

// store.js
import client from "./client";

client.getItems("movies");
```

### Globally available

If you want to make the SDK available in every Vue component, you can inject it into the global Vue context:

```js
// main.js
import Vue from "vue";
import DirectusSDK from "@directus/sdk-js";

const client = new DirectusSDK({
    url: "https://demo-api.directus.app"
});

Object.defineProperties(Vue.prototype, {
    $client: { value: client }
});
```

This allows you to use the client through `this.$client` in any Vue component:

```vue
<template>
    <h1>Hello, world!</h1>
</template>

<script>
export default {
    created() {
        this.$client.getItems("movies");
    }
}
</script>
```
