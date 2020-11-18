<template>
	<div class="no-wrap">{{ value }} {{ options.unit }}</div>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';

export default {
	mixins: [mixin]
};
</script>
