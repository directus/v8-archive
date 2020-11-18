<template>
	<div class="no-wrap">{{ displayValue }}</div>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';

export default {
	mixins: [mixin],
	computed: {
		displayValue() {
			const choices =
				typeof this.options.choices === 'string'
					? JSON.parse(this.options.choices)
					: this.options.choices;

			if (this.options.format) {
				return choices[this.value];
			}

			return this.value;
		}
	}
};
</script>
