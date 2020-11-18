<template>
	<div class="no-wrap">{{ cleanValue }}</div>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';

export default {
	name: 'DisplayWysiwyg',
	mixins: [mixin],
	computed: {
		cleanValue() {
			return this.value ? this.value.replace(/<\/?[^>]+(>|$)/g, '').substring(0, 200) : '';
		}
	}
};
</script>
