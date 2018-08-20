<template>
  <v-input
    v-if="newItem"
    class="input"
    icon-right="vpn_key"
    icon-right-color="null"
    :maxlength="length"
    :type="inputType"
    :readonly="readonly"
    :value="value"
    @input="$emit('input', $event)"></v-input>
  <v-display
    v-else
    :name="name"
    :value="value"
    :type="type"
    :length="length"
    :readonly="readonly"
    :required="required"
    :options="options"></v-display>
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
  max-width: var(--width-small);
}
</style>
