# Layouts

> Layouts are different ways to view or even interact with data on the Item Browse page. Directus includes List and Card layouts out-of-the-box, but others can easily be created to fit specific needs.

## Core Layouts

* **List** — The system default and fallback, this layout is a tabular view that will work with any type of data. It allows you to choose visible columns, sort by specific column data, reorder with drag-and-drop, and more.
* **Card** — Ideal for image-based collections, this layout showcases an image thumbnail with configurable text fields below. It is the default layout for the User Directory and File Library.

## Custom Layouts

These are other potential layouts that could be created to view specific types of data.

* **Map** — Ideal for location based data, this could show each item as a pin on a map.
* **Player** — Ideal for audio file data, this could show the normal List layout with play buttons and a fixed player across the bottom.
* **Calendar** — Ideal for date-time based data, this would show each item as an event on a calendar.
* **Split** — Similar to the List layout, instead of navigating to the Item Detail page when clicking an item, it is opened in a right pane directly on the browse page. Great when you need to quickly see or manage items without losing your place.
* **Spreadsheet** — Also similar to the List layout, this view would work best with "raw" data that can easily be edited _inline_ by clicking into individual cells.
* **Chart** — This is an example of a read-only layout. Here we show the item's data presented as a configurable chart. For instance, you could then toggle between viewing Sales as a normal list and as a chart of data over time.
* **To-Do** — Some layouts could be tailored to specific collection data or functionality. For example, a To-Do list layout might contain dedicated features to nest todos or mark them as complete.

## Files & Structure

A layout is made up out of three required core files. You can create a layout from scratch or use the [extension toolkit](https://github.com/directus/extension-toolkit) to generate boilerplate code.

If not using the extension toolkit, here's how you can create a layout from scratch:
1. Create a meta.json file in `public/extensions/custom/layouts`. See below for an example.
2. Create your .vue files as described below.
3. Transpile the .vue files in the same directory as step #1.
   - (Recommended) If using Parcel, run `parcel build layout.vue -d ./ --no-source-maps --global __DirectusExtension__` (and same for `options.vue`)
   - If using Webpack, [Here's a guide to configuring webpack to compile with vue](https://medium.com/js-dojo/how-to-configure-webpack-4-with-vuejs-a-complete-guide-209e943c4772).

### layout.vue

A standard Vue.js single file component that renders the actual layout like cards in this example.

```vue
<template>
	<div id="grid">
		<div v-for="item in items" class="card" @click="$router.push(item.__link__)">{{ item[viewOptions.title] }}</div>
	</div>
</template>

<script>
import mixin from "../../../mixins/layout";

export default {
  name: "layout-example",
  mixins: [mixin]
}
</script>

<style lang="scss" scoped>

#grid {
	display: grid;
}

.card {
  border-radius: var(--border-radius);
  padding: 5px;
}
</style>
```

### options.vue

A standard Vue.js single file component that renders the options tab when clicking on the info icon. The options allow to define how the layout displays data and what should be displayed. The options can then be accessed in the `layout.vue` file to display the right data.
For more advanced examples, you can look into the `options.vue` file of the [default layouts](https://github.com/directus/api/tree/master/extensions/core/layouts)

```vue
<template>
  <form @submit.prevent>
    <label for="title" class="style-3">title</label>
    <v-select
      :value="viewOptions.title || this.primaryKeyField"
      :options="fieldOptions"
      @input="setOption('title', $event === '__none__' ? null : $event)"
    ></v-select>
  </form>
</template>

<script>
import mixin from "../../../mixins/layout";

export default {
  mixins: [mixin],
  computed: {

    fieldOptions() {
      return {
      	__none__: `dont_show`,
        ...this.$lodash.mapValues(this.fields, info => info.name)
      };
    },
  },
  methods: {

    setOption(field, value) {
      this.$emit("options", {
        ...this.viewOptions,
        [field]: value
      });
    }
  }
};
</script>

<style lang="scss" scoped>
label {
  margin: 10px;
}
</style>
```

### meta.json

The meta.json file contains metadata for the layout, such as its unique name, author, version and translations.

```json
{
  "name": "$t:example",
  "version": "1.0.0"
  }
}
```

## Mixin (props)

We've prepared a [mixin](https://github.com/directus/extension-toolkit/blob/master/mixins/layout.js) that adds all the props to the component that the application passes to the layout. These include items and a bunch of others.
