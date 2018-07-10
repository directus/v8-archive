<template>
	<div v-bind:class="{ inactive: readonly }">
		<flat-pickr class="form-control"
                :config="configs"
                v-model="value"
                @input="$emit('input', $event)">
    </flat-pickr>
  </div>
</template>

<script>
import flatPickr from 'vue-flatpickr-component';
// Need to add base css for flatpickr
import 'flatpickr/dist/flatpickr.css';
// import 'flatpickr/dist/themes/material_red.css';
import mixin from '../../../mixins/interface';

export default {
	name: 'interface-calendar',
	computed: {
		configs() {
			return {
				inline: true,
				minDate: this.options.min,
				maxDate: this.options.max,
				// mode: "range"
			}
		},
	},
	mixins: [mixin],
	components: {
    flatPickr
  },
}
</script>

<style lang="scss">
.interface-calendar {
	max-width: var(--width-small);
}

.inactive {
	pointer-events: none; 
  -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; 
  user-select: none;
}
.flatpickr-input {
	display: none;
}
.flatpickr-day {
	max-width: 40px;
	height: 40px;
}
.flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange, .flatpickr-day.selected.inRange, .flatpickr-day.startRange.inRange, .flatpickr-day.endRange.inRange, .flatpickr-day.selected:focus, .flatpickr-day.startRange:focus, .flatpickr-day.endRange:focus, .flatpickr-day.selected:hover, .flatpickr-day.startRange:hover, .flatpickr-day.endRange:hover, .flatpickr-day.selected.prevMonthDay, .flatpickr-day.startRange.prevMonthDay, .flatpickr-day.endRange.prevMonthDay, .flatpickr-day.selected.nextMonthDay, .flatpickr-day.startRange.nextMonthDay, .flatpickr-day.endRange.nextMonthDay {
	background: var(--red);
	border-color: var(--red);
}
.flatpickr-weekdays,
.flatpickr-months .flatpickr-month {
	background: var(--red);
}
.flatpickr-months .flatpickr-month,
.flatpickr-months .flatpickr-prev-month,
.flatpickr-months .flatpickr-next-month
 {
	color: var(--white);
  fill: var(--white);
}
.flatpickr-current-month .numInputWrapper span.arrowDown:after {
  border-top-color: var(--white);
}
.flatpickr-current-month .numInputWrapper span.arrowUp:after {
  border-bottom-color: var(--white);
}
.flatpickr-months .flatpickr-month {
	height: var(--input-height);
}
.flatpickr-current-month input.cur-year[disabled], .flatpickr-current-month input.cur-year[disabled]:hover {
	color: rgba(255,255,255,0.5);
}
</style>