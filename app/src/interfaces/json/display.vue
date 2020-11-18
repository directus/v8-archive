<template>
	<v-icon v-tooltip.right="tooltipContents" name="code" />
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';

export default {
	mixins: [mixin],
	computed: {
		tooltipContents() {
			return `<pre style="font-family: var(--family-monospace);">${JSON.stringify(
				this.value,
				null,
				4
			)}</pre>`;
		}
	}
};
</script>
