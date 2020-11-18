<template>
	<v-input
		:id="name"
		type="text"
		class="slug"
		:value="value"
		:readonly="disabled"
		:placeholder="options.placeholder"
		:maxlength="length"
		@input="updateValue"
	></v-input>
</template>

<script>
import slug from 'slug';

import mixin from '@directus/extension-toolkit/mixins/interface';

export default {
	mixins: [mixin],
	computed: {
		mirror() {
			const { mirroredField } = this.options;

			return this.values[mirroredField];
		},
		disabled() {
			if (this.readonly === true) return true;

			if (this.options.onlyOnCreate === true && this.newItem === false) return true;

			return false;
		}
	},
	watch: {
		mirror() {
			this.updateValue(this.mirror);
		}
	},
	methods: {
		updateValue(value) {
			if (this.disabled) return;

			this.$emit(
				'input',
				slug(value, {
					lower: this.options.forceLowercase
				})
			);
		}
	}
};
</script>

<style lang="scss" scoped>
.slug {
	max-width: var(--width-medium);
}
</style>
