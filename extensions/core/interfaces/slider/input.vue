<template>
  <v-slider
    class="slider"
    :disabled="readonly"
    :max="options.maximum"
    :min="options.minimum"
    :step="options.step"
    :unit="options.unit"
    :value="safeValue"
    @input="$emit('input', $event);"
  ></v-slider>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  name: "interface-slider",
  mixins: [mixin],
  computed: {
    safeValue() {
      if (!this.value) return this.options.minimum;
      return this.value;
    }
  }
};
</script>

<style lang="scss" scoped>
.slider {
  max-width: var(--width-medium);
}
</style>
