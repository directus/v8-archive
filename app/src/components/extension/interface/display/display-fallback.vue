<template>
	<div class="v-ext-display-fallback">{{ value }}</div>
</template>

<script>
export default {
	name: 'VExtDisplayFallback',
	props: {
		value: {
			type: null,
			default: ''
		}
	}
};
</script>
