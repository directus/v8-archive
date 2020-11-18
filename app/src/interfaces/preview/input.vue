<template>
	<v-button class="launch" :disabled="required && disabled" @click="preview">
		{{ $t('interfaces.preview.preview_item') }}
		<v-icon name="launch" right />
	</v-button>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';

export default {
	mixins: [mixin],
	methods: {
		preview() {
			let url = this.$helpers.micromustache.render(
				this.options.url_template, // "Hello {{text}}!"
				this.values // { text: "World" }
			);

			window.open(url, '_blank');
		}
	}
};
</script>

<style lang="scss" scoped>
.launch {
	margin-top: 10px;
	.material-icons {
		font-size: 18px;
		margin-left: 10px;
		vertical-align: middle;
	}
}
</style>
