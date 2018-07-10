<template>
  <v-details
    :title="options.title"
    :open="options.open">
    <slot />
  </v-details>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin]
};
</script>
