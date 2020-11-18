<template>
	<div class="no-wrap">{{ displayValue }}</div>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';

export default {
	mixins: [mixin],
	computed: {
		displayValue() {
			let value = this.value;
			if (value) {
				const choices =
					typeof this.options.choices === 'string'
						? JSON.parse(this.options.choices)
						: this.options.choices;

				if (this.options.wrapWithDelimiter) {
					value = value.slice(1, -1);
				}

				value = value
					.map(val => {
						if (this.options.formatting === 'text') {
							return choices[val];
						}

						return val;
					})
					.join(', ');
			}

			return value;
		}
	}
};
</script>
