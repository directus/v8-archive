<template>
	<v-select
		:id="name"
		:value="value"
		:disabled="readonly"
		:options="choices"
		:placeholder="options.placeholder"
		@input="$emit('input', $event)"
	></v-select>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';

export default {
	mixins: [mixin],
	computed: {
		choices() {
			const collections = this.$store.state.collections || {};
			const includeSystem = this.options.include_system;

			let choices = {};

			Object.keys(collections)
				.filter(key => {
					if (includeSystem) return key;
					return key.startsWith('directus_') === false;
				})
				.forEach(key => {
					choices[key] = this.$helpers.formatTitle(key);
				});

			return choices;
		}
	}
};
</script>

<style lang="scss" scoped>
.v-select {
	margin-top: 0;
	max-width: var(--width-medium);
}
</style>
