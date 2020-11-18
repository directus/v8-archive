<template>
	<div v-if="options.format" class="no-wrap">
		{{ formatSize(value, options.decimal) }}
	</div>
	<div v-else class="no-wrap">{{ value }}</div>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';
import formatSize from './format-size';

export default {
	mixins: [mixin],
	methods: {
		formatSize
	}
};
</script>
