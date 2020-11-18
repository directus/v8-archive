<template>
	<article class="info-box">
		<header>{{ title }}</header>
		<div class="body"><slot /></div>
	</article>
</template>

<script>
	export default {
		props: {
			title: {
				type: String,
				required: true
			}
		}
	}
</script>

<style lang="scss" scoped>
.info-box {
	border: 1px solid #eeeeee;
	border-radius: 6px;
	overflow: hidden;

	& + .info-box {
		margin-top: 1rem;
	}

	header {
		padding: 0.625rem 0.75rem;
		background-color: #eee;
		text-transform: uppercase;
		font-size: 0.75rem;
		font-weight: 500;
		color: #555;
	}

	.body * {
		margin: 0;
		border-radius: 0;

		&.language-endpoints {
			background-color: transparent;
			
			* {
				color: #2D3E56;
			}
		}
	}
}
</style>