<template>
  <v-select
    :value="value"
    :disabled="readonly"
    :id="name"
    :options="choices"
    :placeholder="options.placeholder"
    @input="$emit('input', $event)" />
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  computed: {
    choices() {
      const collections = this.$store.state.collections || {};
      let choices = {};

      for (var key in collections) {
        if (collections.hasOwnProperty(key) && collections[key].collection) {
          if (
            this.options.include_system ||
            collections[key].collection.startsWith("directus_") === false
          ) {
            choices[key] = this.$helpers.formatTitle(
              collections[key].collection
            );
          }
        }
      }

      return choices;
    }
  }
};
</script>

<style lang="scss" scoped>
.v-select {
  margin-top: 0;
  max-width: var(--width-small);
}
</style>
