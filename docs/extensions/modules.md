# Modules

> Modules are a way to add full-featured modules to Directus. You can build page modules for custom dashboards, reporting, point-of-sale systems, or anything else. Each page is protected within the auth gateway and can easily access project data and global variables.

## Files & Structure

A Module is made up of at least two core files. You can create a module from scratch or use the [extension toolkit](https://github.com/directus/extension-toolkit) to generate boilerplate code.

If not using the extension toolkit, here's how you can create a module from scratch:
1. Create a meta.json file in `public/extensions/custom/modules`. See below for an example.
2. Write up your module in a .vue file.
3. Transpile the .vue file in the same directory as step #1.
   - (Recommended) If using Parcel, run `parcel build module.vue -d ./ --no-source-maps --global __DirectusExtension__`
   - If using Webpack, [Here's a guide to configuring webpack to compile with Vue](https://medium.com/js-dojo/how-to-configure-webpack-4-with-vuejs-a-complete-guide-209e943c4772).

### `module.vue`

A standard Vue.js single file component that renders the content of the Module extension.

```vue
<template>
  <div class="demo-module">
    <v-header title="Demo Module"></v-header>
    <h1 class="style-0">Just an example...</h1>
    <p>
      This is a Directus Module Extension, you can put anything you want in here.
    </p>
  </div>
</template>

<script>
export default {
  name: "demo-module"
};
</script>

<style lang="scss" scoped>
.demo-module {
  padding: var(--page-padding);

  h1 {
    margin-bottom: 20px;
  }
}
</style>
```

### `meta.json`

The meta.json file contains metadata for the Module, such as its unique name, version, and translations.

```vue
{
  "name": "Demo Module",
  "version": "1.0.0",
  "icon": "person",
  "translation": {}
}
```

## Access to API and Settings

From within an extension the API methods provided by the [SDK](../guides/js-sdk.html) can be accessed by the object `this.$api`. Information about the core application's state is contained in `this.$store.state` object, e.g. the user information in `this.$store.state.currentUser`.
