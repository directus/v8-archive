<template>
	<v-input
		:id="name"
		type="number"
		min="0"
		icon-left="swap_vert"
		:value="value"
		:readonly="readonly"
		:maxlength="+length"
		@input="updateValue"
	></v-input>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';

export default {
	mixins: [mixin],
	methods: {
		updateValue(value) {
			this.$emit('input', Number(value));
		}
	}
};
</script>

<style lang="scss" scoped>
.v-input {
	width: 100%;
}
</style>
