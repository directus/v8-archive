<template>
  <span class="no-wrap">{{displayValue}}</span>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  computed: {
    displayValue() {
      if (this.value){
        return this.$helpers.formatTitle(this.value);
      } else {
        return "";
      }
    }
  }
};
</script>
