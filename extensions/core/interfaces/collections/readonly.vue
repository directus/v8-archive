<template>
  <span class="no-wrap">{{displayValue}}</span>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  computed: {
    displayValue() {
      return this.$helpers.formatTitle(this.value);
    }
  }
};
</script>
