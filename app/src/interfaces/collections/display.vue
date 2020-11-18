<template>
	<span class="no-wrap">{{ displayValue }}</span>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';

export default {
	mixins: [mixin],
	computed: {
		displayValue() {
			if (!this.value) return;
			return this.$helpers.formatTitle(this.value);
		}
	}
};
</script>
