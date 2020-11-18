<template>
	<div class="palette">
		<button
			v-for="color in palette"
			:key="color"
			v-tooltip="color.toUpperCase()"
			:style="{ backgroundColor: color }"
			class="color"
			@click="$emit('input', color)"
		/>
	</div>
</template>

<script>
export default {
	props: {
		palette: {
			type: Array,
			required: true,
			default: () => []
		}
	}
};
</script>

<style lang="scss" scoped>
.palette {
	--gap-size: 6px;
	--button-size: calc((var(--input-height) - 12px) / 2);

	display: grid;
	grid-template-columns: repeat(auto-fit, var(--button-size));
	grid-template-rows: repeat(2, var(--button-size));
	grid-gap: var(--gap-size);
	grid-auto-flow: column;

	.color {
		display: block;
		width: var(--button-size);
		height: var(--button-size);
		border-radius: 50%;
	}
}
</style>
