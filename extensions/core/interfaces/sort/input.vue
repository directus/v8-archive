<template>
  <v-input
    type="number"
    min="0"
    icon-left="swap_vert"
    :value="value"
    :readonly="readonly"
    :maxlength="+length"
    :id="name"
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
  width: 100%;
  max-width: var(--width-small);
}
</style>
