<template>
	<div
  	:style="{ backgroundColor: `var(--${value})`}"
  	v-tooltip="tooltipValue"></div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  computed: {
    tooltipValue() {
      return this.value ? this.$helpers.formatTitle(this.value) : null;
    }
  }
};
</script>

<style lang="scss" scoped>
div {
  width: 20px;
  height: 20px;
  border-radius: 50%;
}
</style>
