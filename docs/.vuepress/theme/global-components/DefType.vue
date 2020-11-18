<template>
	<span class="def-type" :class="{ alert }"><slot /></span>
</template>

<script>
	export default {
		props: {
			alert: {
				type: Boolean,
				default: false
			}
		}
	}
</script>

<style lang="scss" scoped>
.def-type {
	font-size: 0.75rem;
	color: #aaa;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif;

	&.alert {
		text-transform: uppercase;
		color: #ff9800;
	}
}
</style>