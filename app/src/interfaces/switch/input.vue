<template>
	<div class="interface-switch">
		<v-checkbox
			v-if="options.checkbox"
			:inputValue="value"
			@change="emitValue"
			:label="label"
			:disabled="readonly"
		/>
		<v-switch
			v-else
			:inputValue="value"
			@change="emitValue"
			:label="label"
			:disabled="readonly"
		/>
	</div>
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
		readonly: {
			type: Boolean,
			default: false
		},
		options: {
			type: Object as () => Options,
			required: true
		}
	},
	setup(props, { emit }) {
		const emitValue = (value: boolean) => emit('input', value);
		const label = computed<string>(() =>
			props.value ? props.options.labelOn : props.options.labelOff
		);

		return { emitValue, label };
	}
});
</script>

<style lang="scss" scoped>
.interface-switch {
	height: var(--input-height);
	display: flex;
	align-items: center;
}
</style>
