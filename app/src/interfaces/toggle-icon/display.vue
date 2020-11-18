<template>
	<div>
		<v-icon class="readonly-toggle-icon" :color="`--${colorChange}`" :name="icon" />
		<span :style="{ color: `var(--${colorChange})` }">{{ textChange }}</span>
	</div>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';

export default {
	name: 'ReadonlyToggleIcon',
	mixins: [mixin],
	computed: {
		icon() {
			return this.value ? this.options.iconActive : this.options.iconInactive;
		},
		colorChange() {
			return this.value ? this.options.colorActive : this.options.colorInactive;
		},
		textChange() {
			return this.value ? this.options.textActive : this.options.textInactive;
		}
	}
};
</script>

<style lang="scss" scoped>
div > span {
	text-transform: uppercase;
	line-height: 1.18;
	font-weight: 700;
	font-size: 0.85em;
}
</style>
