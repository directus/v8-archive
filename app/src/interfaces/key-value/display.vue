<template>
	<v-icon v-tooltip="displayValue" name="list_alt" />
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';
export default {
	mixins: [mixin],
	computed: {
		displayValue() {
			if (this.value === null) return null;

			const displayValue = Object.keys(this.value)
				.map(key => {
					return `${key}: ${this.value[key]}`;
				})
				.join('\n');

			return `<pre>${displayValue}</pre>`;
		}
	}
};
</script>
