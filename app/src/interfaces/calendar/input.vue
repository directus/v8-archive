<template>
	<div :class="{ inactive: readonly }">
		<flat-pickr
			class="form-control"
			:config="configs"
			:value="value"
			@input="$emit('input', $event)"
		></flat-pickr>
	</div>
</template>

<script>
import flatPickr from 'vue-flatpickr-component';
import mixin from '@directus/extension-toolkit/mixins/interface';

import './calendar.css';

export default {
	name: 'InterfaceCalendar',
	components: {
		flatPickr
	},
	mixins: [mixin],
	computed: {
		configs() {
			return {
				inline: true,
				minDate: this.options.min,
				maxDate: this.options.max,
				dateFormat: 'Y-m-d'
			};
		}
	}
};
</script>

<style lang="scss" scoped>
.inactive {
	pointer-events: none;
	-webkit-user-select: none;
	-moz-user-select: none;
	-ms-user-select: none;
	user-select: none;
}
</style>

<style lang="scss">
.flatpickr-calendar {
	background-color: var(--input-background-color);
}
</style>
