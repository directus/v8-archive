<template>
	<div v-if="!checkbox">
		<input
		type="checkbox"
		:id="icon-toggle"
		@change="updateValue($event.target.checked)">
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
  },
  methods: {
    updateValue(value) {
      this.$emit("input", value);
    }
  }
};
</script>

<style type="scss" scoped>
div {
  position: relative;
}
input {
  position: absolute;
  visibility: collapse;
}
</style>
