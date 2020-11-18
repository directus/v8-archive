<template>
	<v-icon :name="icon" :color="iconColor" />
</template>

<script lang="ts">
import { createComponent, computed } from '@vue/composition-api';
import { Options } from './switch.options';

export default createComponent({
	props: {
		value: {
			type: Boolean,
			default: null
		},
		options: {
			type: Object as () => Options,
			required: true
		}
	},
	setup(props) {
		const icon = computed<string>(() => {
			if (props.options.checkbox) {
				return props.value ? 'check_box' : 'check_box_outline_blank';
			}

			return props.value ? 'check' : 'close';
		});

		const iconColor = computed<string>(() => {
			return props.value ? '--input-border-color-focus' : '--input-border-color';
		});

		return { icon, iconColor };
	}
});
</script>

<style lang="scss" scoped>
.close {
	opacity: 0.3;
}
</style>
