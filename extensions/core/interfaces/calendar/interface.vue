<template>
	<div v-bind:class="{ active: readonly }">
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
import 'flatpickr/dist/themes/material_red.css';
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

<style lang="scss" scoped>
.interface-calendar {
	max-width: var(--width-small);
}
.active {
	pointer-events: none; 
  -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; 
  user-select: none;
}
.flatpickr-input {
	display: none;
}
</style>