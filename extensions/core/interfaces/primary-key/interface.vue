<template>
  <v-input
    v-if="newItem"
    :class="input"
    :maxlength="length"
    :type="inputType"
    :readonly="readonly"
    :value="value"
    @input="$emit('input', $event)" />
  <readonly
    v-else
    :name="name"
    :value="value"
    :type="type"
    :length="length"
    :readonly="readonly"
    :required="required"
    :loading="loading"
    :options="options" />
</template>

<script>
import mixin from "../../../mixins/interface";
import Readonly from "./readonly.vue";

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
    Readonly
  }
};
</script>

<style lang="scss" scoped>
.input {
  max-width: var(--width-small);
}
</style>
