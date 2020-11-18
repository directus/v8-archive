<template>
	<div>{{ displayValue }}</div>
</template>

<script lang="ts">
import { createComponent, computed, PropType, Ref } from '@vue/composition-api';
import { CalendarOptions } from './types';
import useTimeFromNow from '@/compositions/time-from-now';
import format from 'date-fns/format';

export default createComponent({
	props: {
		options: {
			type: Object as PropType<CalendarOptions>,
			required: true
		},
		value: {
			type: String,
			default: null
		}
	},
	setup(props) {
		let valueAsDate: Date;
		let timeAgo: Ref<string>;

		if (props.value) {
			valueAsDate = new Date(props.value.replace(/-/g, '/'));
			timeAgo = useTimeFromNow(valueAsDate, 0);
			const now = new Date();
		}

		const displayValue = computed<string>(() => {
			if (!props.value) return '--';

			if (!props.options.formatting) {
				return timeAgo.value;
			}

			return format(valueAsDate, props.options.formatting);
		});

		return { displayValue };
	}
});
</script>
