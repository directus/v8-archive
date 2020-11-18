<template>
	<div class="buttons no-wrap">
		<v-tag v-for="value in displayValue" :key="value">
			{{ value }}
		</v-tag>
	</div>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';

export default {
	mixins: [mixin],
	computed: {
		displayValue() {
			if (!this.value) return '';

			let value = this.type === 'array' ? this.value : this.value.split(',');

			if (this.options.wrap) {
				value.pop();
				value.shift();
			}

			if (this.options.format) {
				value = value.map(tag => this.$helpers.formatTitle(tag));
			}

			return value;
		}
	}
};
</script>

<style lang="scss" scoped>
.buttons {
	display: flex;
	flex-wrap: wrap;
	padding: 5px 0;

	span {
		margin: 2px;
		padding: 2px 4px 3px;
		background-color: var(--blue-grey-300);
		border-radius: var(--border-radius);
		color: var(--white);
		font-weight: 500;
		font-size: 13px;
	}
}
</style>
