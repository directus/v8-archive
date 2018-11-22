<template>
  <v-input
    type="number"
    :readonly="readonly"
    :placeholder="options.placeholder"
    :value="value"
    :icon-left="options.iconLeft"
    :icon-right="options.iconRight"
    :step="step"
    @input="$emit('input', Number($event));"
  ></v-input>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  computed: {
    step() {
      if (this.type === "decimal") {
        const decimalPlaces = this.length && this.length.split(",")[1];
        return `0.${"1".padStart(decimalPlaces, 0)}`;
      }

      return 1;
    }
  }
};
</script>

<style lang="scss" scoped>
.v-input {
  width: 100%;
  max-width: var(--width-medium);
}
</style>
