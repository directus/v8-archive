<template>
	<div>{{ displayValue }}</div>
</template>

<script lang="ts">
import { createComponent, computed, PropType } from '@vue/composition-api';
import { DateTimeOptions } from './types';
import useTimeFromNow from '@/compositions/time-from-now';

const { i18n } = require('@/lang/');

export default createComponent({
	props: {
		value: {
			type: String,
			default: null
		},
		options: {
			type: Object as PropType<DateTimeOptions>,
			required: true
		}
	},
	setup(props) {
		const displayValue = computed<string>(() => {
			if (!props.value) return '--';

			const date = new Date(props.value);

			if (props.options.showRelative) {
				return useTimeFromNow(date).value;
			}

			return i18n.d(date, 'long');
		});

		return { displayValue };
	}
});
</script>
