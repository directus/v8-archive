<template>
	<v-icon class="material-icons readonly-password" :color="`--${colorChange}`" :name="icon" />
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';

export default {
	name: 'ReadonlyPassword',
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
