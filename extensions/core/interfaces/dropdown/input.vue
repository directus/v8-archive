<template>
  <v-select
    :value="value"
    :disabled="readonly"
    :id="name"
    :options="choices"
    :placeholder="options.placeholder"
    :icon="options.icon"
    @input="$emit('input', $event)"></v-select>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  computed: {
    choices() {
      let choices = this.options.choices;

      if (!choices) return {};

      if (typeof this.options.choices === "string") {
        choices = JSON.parse(this.options.choices);
      }

      return choices;
    }
  }
};
</script>

<style lang="scss" scoped>
.v-select {
  margin-top: 0;
  max-width: var(--width-medium);
}
</style>
