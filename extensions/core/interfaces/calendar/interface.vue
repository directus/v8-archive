<template>
	<div v-bind:class="{ inactive: readonly }">
		<flat-pickr class="form-control"
                :config="configs"
                :value="value"
                @input="$emit('input', $event)">
    </flat-pickr>
  </div>
</template>

<script>
import flatPickr from 'vue-flatpickr-component';
// Need to add base css for flatpickr
import 'flatpickr/dist/flatpickr.css';
import './calendar.css';
// import 'flatpickr/dist/themes/material_red.css';
import mixin from '../../../mixins/interface';

export default {
	name: 'interface-calendar',
	computed: {
		// dateFormat() {
		// 	if (this.options.formatting == "" || this.options.formatting == null) {
		// 		return "Y-m-d";
		// 	}
		// 		return this.options.formatting;
		// },
		configs() {
			return {
				inline: true,
				minDate: this.options.min,
				maxDate: this.options.max,
				dateFormat: "Y-m-d",
				// dateFormat: this.dateFormat,
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

<style lang="scss" scoped>
.interface-calendar {
	max-width: var(--width-small);
}
.inactive {
	pointer-events: none; 
  -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; 
  user-select: none;
}
</style>