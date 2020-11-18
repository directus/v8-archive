<template>
	<v-icon :name="icon" />
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';

export default {
	name: 'Display2faSecret',
	mixins: [mixin],
	computed: {
		icon() {
			return this.value ? 'lock' : 'lock_open';
		}
	}
};
</script>

<style scoped></style>
