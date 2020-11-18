<template>
	<div
		v-tooltip="tooltipValue"
		class="swatch"
		:class="{ light: tooLight(value) }"
		:style="{ backgroundColor: `var(--${value})` }"
	></div>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';

export default {
	mixins: [mixin],
	computed: {
		tooltipValue() {
			return this.value ? this.$helpers.formatTitle(this.value) : null;
		}
	},
	methods: {
		tooLight(color) {
			if (!color) return null;

			return color.includes('white') ||
				color.includes('100') ||
				(color.includes('50') && !color.includes('500'))
				? true
				: false;
		}
	}
};
</script>

<style lang="scss" scoped>
.swatch {
	transition: var(--fast) var(--transition);
	display: inline-block;
	width: 24px;
	height: 24px;
	border-radius: 100%;
	vertical-align: middle;
	margin-right: 4px;
	color: var(--white);
	text-align: center;
	&.light {
		border: 1px solid var(--blue-grey-300);
		color: var(--blue-grey-900) !important;
		i {
			line-height: 22px;
		}
	}
	i {
		line-height: 24px;
	}
}
</style>
