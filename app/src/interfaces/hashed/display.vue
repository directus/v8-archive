<template>
	<v-icon :name="icon" class="readonly-hashed" :color="`--${colorChange}`" />
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';

export default {
	name: 'ReadonlyHashed',
	mixins: [mixin],
	computed: {
		icon() {
			return this.value ? 'lock_outline' : 'lock_open';
		},
		colorChange() {
			return this.value ? 'blue-grey-600' : 'warning';
		}
	}
};
</script>

<style lang="scss" scoped></style>
