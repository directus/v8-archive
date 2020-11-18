<template>
	<div class="no-wrap">
		{{ this.value.length }} File{{
			this.value.length === 0 || this.value.length > 1 ? 's' : null
		}}
	</div>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';

export default {
	mixins: [mixin]
};
</script>
