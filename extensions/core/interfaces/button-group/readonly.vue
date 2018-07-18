<template>
  <!-- WIP -->
  <div class="readonly-button-group no-wrap">{{value?this.$helpers.formatTitle(value):''}}</div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
	name: "readonly-button-group",
	mixins: [mixin]
};
</script>
