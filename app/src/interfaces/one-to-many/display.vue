<template>
	<v-contextual-menu
		trigger="hover"
		:text="itemCount"
		:options="menuOptions"
		:icon="null"
		placement="right-end"
	/>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';
import { forEach } from 'lodash';

export default {
	name: 'ReadonlyOneToMany',
	mixins: [mixin],
	computed: {
		itemCount() {
			return this.$tc('item_count', (this.value || []).length, {
				count: (this.value || []).length
			});
		},
		menuOptions() {
			var options = [];
			forEach(this.value, value => {
				options.push({
					text: this.$helpers.micromustache.render(this.options.template, value)
				});
			});
			return options;
		}
	}
};
</script>

<style lang="scss" scoped>
.v-ext-display {
	display: flex;
}
</style>
