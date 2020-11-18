<template>
	<div class="two-up">
		<div class="left">
			<slot name="left" />
		</div>

		<div class="right">
			<slot name="right" />
		</div>
	</div>
</template>

<style lang="scss" scoped>
.two-up {
	margin: 12px 0;
	display: grid;
	grid-gap: 3rem;
	grid-template-columns: 1fr;

	> div {
		min-width: 0;
	}

	@media (min-width: 1158px) {
		grid-template-columns: 4fr 3fr;
	}
}

.left ::v-deep h3 {
	margin-top: 2em;
}

.right ::v-deep .sticky {
	position: sticky;
	top: 5rem;
}
</style>