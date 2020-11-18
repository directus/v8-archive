<template>
	<div>{{ displayValue }}</div>
</template>

<script lang="ts">
import { DateOptions } from './types';
import { createComponent, computed, PropType } from '@vue/composition-api';
import timeFromNow from '@/compositions/time-from-now';

const { i18n } = require('@/lang/');

export default createComponent({
	props: {
		value: {
			type: String,
			default: null
		},
		options: {
			type: Object as PropType<DateOptions>,
			required: true
		}
	},
	setup(props) {
		const displayValue = computed<string>(() => {
			if (!props.value) return '--';

			const date = new Date(props.value.replace(/-/g, '/'));

			if (props.options.localized) {
				return i18n.d(date, 'short');
			}

			if (props.options.showRelative) {
				return timeFromNow(date, 0);
			}

			return props.value;
		});

		return { displayValue };
	}
});
</script>
