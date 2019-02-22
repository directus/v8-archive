<template>
  <v-input
    type="datetime-local"
    class="interface-datetime"
    :id="name"
    :name="name"
    :min="options.min"
    :max="options.max"
    :readonly="readonly"
    :value="formattedValue"
    :icon-left="options.iconLeft"
    :icon-right="options.iconRight"
    @input="updateValue"
  ></v-input>
</template>

<script>
import mixin from "../../../mixins/interface";
import format from "date-fns/format";
import parse from "date-fns/parse";

export default {
  mixins: [mixin],
  computed: {
    formattedValue() {
      if (!this.value) return null;
      return format(new Date(this.value), "YYYY-MM-DDTHH:mm:ss");
    }
  },
  created() {
    if (this.options.defaultToCurrentDatetime && !this.value) {
      this.$emit("input", new Date());
    }
  },
  methods: {
    updateValue(value) {
      if (!value) return;

      return this.$emit(
        "input",
        format(parse(value), "YYYY-MM-DD HH:mm:ss")
      );
    }
  }
};
</script>

<style lang="scss" scoped>
.interface-datetime {
  max-width: var(--width-medium);
}
</style>
