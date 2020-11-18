<template>
	<v-slider
		class="slider"
		:disabled="readonly"
		:max="options.maximum"
		:min="options.minimum"
		:step="options.step"
		:unit="options.unit"
		:value="safeValue"
		@input="$emit('input', $event)"
	></v-slider>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';

export default {
	name: 'InterfaceSlider',
	mixins: [mixin],
	computed: {
		safeValue() {
			if (!this.value && (this.value !== 0 || this.options.minimum > 0))
				return this.options.minimum;
			return this.value;
		}
	}
};
</script>

<style lang="scss" scoped>
.slider {
	max-width: var(--width-medium);
}
</style>
