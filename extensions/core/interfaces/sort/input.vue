<template>
  <v-input
    type="number"
    min="0"
    :class="width"
    :value="value"
    :readonly="readonly"
    :placeholder="options.placeholder"
    :maxlength="+length"
    :id="name"
    :charactercount="options.showCharacterCount"
    @input="updateValue"></v-input>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  methods: {
    updateValue(value) {
      this.$emit("input", Number(value));
    }
  }
};
</script>

<style lang="scss" scoped>
.v-input {
  max-width: var(--width-small);
}
</style>
