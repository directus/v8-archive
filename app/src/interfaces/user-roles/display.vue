<template>
	<div>{{ displayValue }}</div>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';

export default {
	name: 'DisplayUserRole',
	mixins: [mixin],
	computed: {
		displayValue() {
			return this.value && this.value.name;
		}
	}
};
</script>

<style lang="scss" scoped></style>
