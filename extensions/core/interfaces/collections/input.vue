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
      const includeSystem = this.options.include_system;

      let choices = {};

      Object.keys(collections).forEach(key => {
        if (includeSystem === false && key.startsWith("directus_")) return;

        choices[key] = this.$helpers.formatTitle(key);
      });

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
