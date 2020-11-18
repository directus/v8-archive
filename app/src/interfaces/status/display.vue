<template>
	<div
		v-tooltip="options.simpleBadge ? currentStatus.name : false"
		:class="['badge', 'no-wrap', { simple: options.simpleBadge }]"
		:style="style"
	>
		{{ options.simpleBadge ? null : currentStatus.name }}
	</div>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';

export default {
	mixins: [mixin],
	computed: {
		currentStatus() {
			return this.options.status_mapping[this.value];
		},
		style() {
			return {
				backgroundColor: `var(--${this.currentStatus.background_color})`,
				color: `var(--${this.currentStatus.text_color})`
			};
		}
	}
};
</script>

<style lang="scss" scoped>
.badge {
	border-radius: var(--border-radius);
	padding: 5px 8px 4px;
	font-weight: var(--weight-bold);
	display: block;
	cursor: default;
	max-width: max-content;
	text-overflow: ellipsis;
}

.simple {
	border-radius: 50%;
	width: 12px;
	height: 12px;
	padding: 6px;
}
</style>
