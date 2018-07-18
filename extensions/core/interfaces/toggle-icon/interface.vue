<template>
	<div class="interface-toggle-icon">
		<input
  		type="checkbox"
  		id="icon-toggle"
  		@change="updateValue($event.target.checked)">
		<label for="icon-toggle" :style="{ color: `var(--${colorChange})` }">
			<i class="material-icons">{{ icon }}</i>
		</label>
	</div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  name: "interface-toggle-icon",
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

<style lang="scss" scoped>
.interface-toggle-icon {
  user-select: none;
  cursor: pointer;
}

input {
  position: absolute !important;
  height: 1px;
  width: 1px;
  overflow: hidden;
  clip: rect(1px, 1px, 1px, 1px);
  clip-path: polygon(0px 0px, 0px 0px, 0px 0px, 0px 0px);
}
</style>
