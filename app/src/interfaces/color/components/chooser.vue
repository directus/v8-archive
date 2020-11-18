<template>
	<div class="chooser">
		<v-simple-select v-model="format">
			<option value="rgb">RGB</option>
			<option value="hsl">HSL</option>
		</v-simple-select>

		<sliders :format="format" :value="value" @input="$emit('input', $event)" />
	</div>
</template>

<script>
import Sliders from './sliders';

export default {
	components: {
		Sliders
	},
	props: {
		value: {
			type: String,
			default: null
		}
	},
	data() {
		return {
			format: 'rgb'
		};
	}
};
</script>

<style lang="scss" scoped>
.chooser {
	padding: 8px;
	width: var(--form-column-width);
}
</style>
