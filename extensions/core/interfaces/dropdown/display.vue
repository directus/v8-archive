<template>
  <span class="no-wrap">{{ displayValue }}</span>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  computed: {
    choices() {
      return typeof this.options.choices === "string"
        ? JSON.parse(this.options.choices)
        : this.options.choices;
    },
    displayValue() {
      if (this.options.formatting) {
        return this.choices[this.value];
      }

      return this.value;
    }
  }
};
</script>
