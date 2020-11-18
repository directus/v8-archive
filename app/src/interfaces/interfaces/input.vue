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
import { mapState } from 'vuex';

export default {
	mixins: [mixin],
	computed: {
		...mapState({
			interfaces: state => state.extensions.interfaces || {}
		}),
		choices() {
			let choices = {};

			let interfaceNames = Object.keys(this.interfaces);

			if (this.options.relational === false) {
				interfaceNames = interfaceNames.filter(key => {
					return this.interfaces[key].relation === undefined;
				});
			}

			if (this.options.status === false) {
				interfaceNames = interfaceNames.filter(key => {
					return this.interfaces[key].types[0] !== 'status';
				});
			}

			interfaceNames.forEach(key => {
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
