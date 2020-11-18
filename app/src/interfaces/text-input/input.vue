<template>
	<v-input
		:id="name"
		type="text"
		icon-right-color=""
		:value="value || ''"
		:readonly="readonly"
		:monospace="options.monospace"
		:placeholder="options.placeholder"
		:icon-left="options.iconLeft"
		:icon-right="options.iconRight"
		:maxlength="length ? +length : null"
		:charactercount="options.showCharacterCount"
		@input="updateValue"
	></v-input>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';

export default {
	mixins: [mixin],
	methods: {
		updateValue(rawValue) {
			let value = rawValue;

			if (this.options.trim) {
				if (!this.value || value.length > this.value.length) {
					value = value.trim();
				}
			}

			this.$emit('input', value);
		}
	}
};
</script>

<style lang="scss" scoped>
.v-input {
	width: 100%;
	max-width: var(--width-medium);
}
</style>
