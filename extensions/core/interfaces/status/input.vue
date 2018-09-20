<template>
  <div class="interface-status">
    <v-radio
      v-for="(options, key) in optionValues"
      :id="`${fields[name].collection}-${name}-${key}`"
      :name="name"
      :value="key"
      :key="key"
      :model-value="String(value)"
      :label="$t(options.name)"
      :checked="key == value"
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
      if (typeof this.options.status_mapping === "string") {
        return this.options.status_mapping
          ? JSON.parse(this.status_mapping)
          : {};
      }

      return this.options.status_mapping || {};
    }
  }
};
</script>

<style lang="scss" scoped>
.interface-status {
  display: grid;
  grid-auto-flow: column;
  grid-auto-columns: max-content;
  grid-gap: 40px;
}
</style>
