<template>
	<div class="def-list">
		<slot />
	</div>
</template>

<style lang="scss" scoped>

.def-list > *:last-child {
	padding-bottom: 1rem;
	border-bottom: 1px solid #eee;
}

::v-deep {
	h4, p {
		margin: 0;
	}

	p {
		font-size: 14px;
	}

	h4 {
		margin-top: 1rem;
		padding-top: 1rem;
		border-top: 1px solid #eee;
		font-family: source-code-pro, Menlo, Monaco, Consolas, "Courier New", monospace;
		font-size: 0.8rem;
	}

	code {
		background-color: transparent;
		padding: 0;
	}
}
</style>