<template>
	<div>
		<label :for="icon-toggle" :style="{color: `var(--${colorChange})`}">
			<i class="material-icons">{{ icon }}</i>
		</label>
	</div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  computed: {
    icon() {
      return this.value ? this.options.iconActive : this.options.iconInactive;
    },
    colorChange() {
      return this.value ? this.options.colorA : this.options.colorB;
    }
  }
};
</script>

<style type="scss" scoped>
</style>
