<template>
	<span v-if="options.localized" class="no-wrap">{{ $n(value) }}</span>
	<span v-else class="no-wrap">{{ value }}</span>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';

export default {
	mixins: [mixin]
};
</script>
