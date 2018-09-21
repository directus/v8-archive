<template>
  <div>
    <v-input
      class="input"
      :maxlength="length"
      :type="inputType"
      :readonly="!newItem"
      :value="value"
      @input="$emit('input', $event)"></v-input>
    <span v-if="!newItem" class="immutable">{{ $t('interfaces-primary-key-immutable') }}</span>
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";
import VDisplay from "./display.vue";

export default {
  mixins: [mixin],
  computed: {
    inputType() {
      if (this.type === "VARCHAR" || this.type === "CHAR") {
        return "text";
      }

      return "number";
    }
  },
  components: {
    VDisplay
  }
};
</script>

<style lang="scss" scoped>
.input {
  width: 100%;
  max-width: var(--width-normal);
}
.immutable {
  display: block;
  color: var(--light-gray);
  text-transform: initial;
  margin-right: 20px;
  font-style: italic;
  margin-top: 4px;
}
</style>
