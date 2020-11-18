<template>
	<v-input :value="value" class="v-ext-input-fallback" @input="$emit('input', $event)" />
</template>

<script>
export default {
	name: 'VExtInputFallback',
	props: {
		value: {
			type: null,
			default: ''
		},
		inputName: {
			type: String,
			default: ''
		},
		name: {
			type: String,
			default: ''
		}
	},
	created() {
		if (this.inputName == null) {
			this.$notify({
				title: this.$t('no_interface_error', {
					field: this.$helpers.formatTitle(this.name)
				}),
				color: 'red',
				iconMain: 'error'
			});
		} else {
			this.$notify({
				title: this.$t('extension_error', { ext: this.inputName }),
				color: 'red',
				iconMain: 'error'
			});
		}
	}
};
</script>

<style lang="scss" scoped>
.v-ext-input-fallback {
	max-width: var(--width-medium);
}
</style>
