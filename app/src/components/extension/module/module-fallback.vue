<template>
	<div />
</template>

<script>
export default {
	name: 'VExtPageFallback',
	created() {
		this.$notify({
			title: this.$t('extension_error', { ext: this.interfaceName }),
			color: 'red',
			iconMain: 'error'
		});
	}
};
</script>
