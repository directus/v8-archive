<template>
	<a :href="url" class="no-wrap" target="_blank">
		{{ $t('interfaces.preview.preview_item') }}
	</a>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';

export default {
	mixins: [mixin],
	computed: {
		url() {
			return this.$helpers.micromustache.render(
				// this.values doesn't exist on the Browse Items page yet. It will be added soon, and then this will work
				this.options.url_template, // "Hello {{text}}!"
				this.values // { text: "World" }
			);
		}
	}
};
</script>
