<template>
	<div>{{ itemCount }}</div>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';

export default {
	name: 'ReadonlyManyToMany',
	mixins: [mixin],
	computed: {
		itemCount() {
			return this.$tc('item_count', (this.value || []).length, {
				count: (this.value || []).length
			});
		}
	}
};
</script>
