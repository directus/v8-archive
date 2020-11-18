<template>
	<div class="readonly-checkboxes no-wrap">{{ displayValue }}</div>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';

export default {
	name: 'ReadonlyCheckboxes',
	mixins: [mixin],
	computed: {
		selection() {
			if (this.value == null) return [];

			const selection = this.type === 'VARCHAR' ? this.value.split(',') : this.value;
			if (this.options.wrap) {
				selection.pop();
				selection.shift();
			}
			return selection;
		},
		displayValue() {
			let display = this.selection ? this.selection : [];
			if (this.options.formatting && this.type === 'array') {
				return display
					.map(val => (this.options.choices[val] ? this.options.choices[val] : val))
					.toString();
			}
			return display.toString();
		}
	}
};
</script>
