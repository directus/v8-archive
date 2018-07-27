<template>
  <div class="interface-status">
    <v-radio
      v-for="(options, key) in optionValues"
      :id="`${name}-${key}`"
      :name="name"
      :value="key"
      :key="key"
      :model-value="value"
      :label="$t(options.name)"
      :checked="key === value"
      @change="$emit('input', $event)"></v-radio>
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  name: "interface-status",
  mixins: [mixin],
  computed: {
    optionValues() {
      if (typeof this.options.statusMapping === "string") {
        return this.options.statusMapping ? JSON.parse(this.statusMapping) : {};
      }

      return this.options.statusMapping || {};
    }
  }
};
</script>

<style lang="scss" scoped>
.interface-status {
  display: grid;
  grid-auto-flow: column;
  grid-auto-columns: max-content;
  grid-gap: 20px;
}
</style>
