<template>
	<div class="readonly-button-group no-wrap">
		{{ value ? $helpers.formatTitle(value) : '' }}
	</div>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';

export default {
	name: 'ReadonlyButtonGroup',
	mixins: [mixin]
};
</script>
