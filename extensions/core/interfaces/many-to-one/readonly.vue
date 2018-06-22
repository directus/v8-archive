w<template>
  <div>{{displayValue}}</div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  computed: {
    displayValue() {
      return this.$helpers.micromustache.render(
        this.options.template,
        this.value
      );
    }
  }
};
</script>
