<template>
  <v-details
    :title="options.title ? options.title : 'Field Group'"
    :open="options.open"
  >
    <slot></slot>
  </v-details>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin]
};
</script>
