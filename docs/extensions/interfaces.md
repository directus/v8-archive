# Interfaces

> Interfaces allow for different ways of viewing and interacting with field data. These interfaces are primarily used on the edit form of the Item Detail page, but also render readonly data on the Item Browse page.

## Files & Structure

An interface is made up out of three required core files. You can create a layout from scratch or use the [extension toolkit](https://github.com/directus/extension-toolkit) to generate boilerplate code.

If not using the extension toolkit, here's how you can create an interface from scratch:
1. Create a meta.json file in `public/extensions/custom/interfaces`. See below for an example.
2. Create your .vue files as described below.
3. Transpile the .vue files in the same directory as step #1.
   - (Recommended) If using Parcel, run `parcel build input.vue -d ./ --no-source-maps --global __DirectusExtension__` (and same for `display.vue`)
   - If using Webpack, [Here's a guide to configuring webpack to compile with vue](https://medium.com/js-dojo/how-to-configure-webpack-4-with-vuejs-a-complete-guide-209e943c4772).


### `input.vue`

A standard Vue.js single file component that renders the actual interface and emits the value on create/update. This is what is shown on the Item Detail page. For example the Color interface shows a palette of clickable color options.

```vue
<template>
  <input :value="value" @input="$emit('input')" />
</template>

<script>
import { interfaceMixin } from "@directus/vue-mixins";

export default {
  name: "interface-example",
  mixins: [interfaceMixin]
}
</script>

<style lang="scss" scoped>
input {
  border-radius: var(--border-radius);
}
</style>
```

### `display.vue`

A standard Vue.js single file component that renders a readonly version of the value. This is what is shown on the Item Browse page (depending on the selected layout). For example the Color interface shows a swatch of the saved value.

```vue
<template>
  <div class="class-name">
    {{value}}
  </div>
</template>

<script>
import { interfaceMixin } from "@directus/vue-mixins";

export default {
  name: "readonly-example",
  mixins: [interfaceMixin]
}
</script>

<style lang="scss" scoped>
.class-name {
  color: var(--accent);
}
</style>
```

### `meta.json`

The meta.json file contains metadata for the interface, such as its unique name, author, version, interface options, and translations.

```json
{
  "name": "Interface Example",
  "version": "1.0.0"
}
```

#### Options

* `name` — REQUIRED. The unique name of this interface
* `version` — REQUIRED. Whenever updating an extension it is important to increment the version number in accordance with [SemVer](https://semver.org/)
* `types` — An array of allowed Directus Field Types
* `fieldset` — TODO
* `icon` — The name of a Material Design icon to represent the interface within Settings
* `options` — Define the options available to this interface
* `recommended` — Recommended values used during creation of the field (eg: set a recommnded length or default)
* `translation` — JSON of translations used by the interface
* `hideLabel` — Can be set to `true` to hide the field label on the Item Detail page

## States

Every interface should support a `readonly` and a `disabled` state.

## Mixin (props)

We've prepared a [mixin](https://github.com/directus/extension-toolkit/blob/master/mixins/interface.js) that adds all the props to the component that the application passes to the interface. These include value, collection, relationship, and a bunch of others. A minimal interface mostly uses `value` and `options`.

## Testing

There is an interface debugger under Directus App Settings which you can use to test all the different properties and options of any interface available within the connected API.

## Styling

Directus uses CSS Custom Properties across the application to enable theming and style consistencies. Check the [`global.scss`](https://github.com/directus/app/blob/master/src/assets/global.scss) file in the app for the full list of available variables.

### Colors

The full material design color palette is for use in custom properties, eg: `var(--red-50)` or `var(--deep-purple-500)`. However, we recommend sticking to the following color names, seeing these are the ones that will be overridden for theming:

```
var(--lightest-gray);
var(--lighter-gray);
var(--light-gray);
var(--gray);
var(--dark-gray);
var(--darker-gray);
var(--darkest-gray);

var(--accent);  // user configurable accent color
var(--action);  // color of action buttons, defaults to light-blue
var(--success); // something went well, green
var(--warning); // user's attention is needed before proceeding, yellow
var(--danger);  // something failed or the action has irreversible side effects; red
```
