<template>
  <div class="no-wrap">{{displayValue}}</div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  computed: {
    displayValue() {
      if (this.options.formatting) {
        return this.options.choices[this.value];
      }

      return this.value;
    }
  }
};
</script>
