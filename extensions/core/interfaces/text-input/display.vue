<template>
  <div class="no-wrap">{{ displayValue }}</div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  computed: {
    displayValue() {
      let value = this.value;

      if (this.options.formatValue) {
        value = this.$helpers.formatTitle(value);
      }

      return value;
    }
  }
};
</script>
