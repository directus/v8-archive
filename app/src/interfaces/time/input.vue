<template>
	<v-input
		type="time"
		class="input"
		:readonly="readonly"
		:value="value"
		@input="emitValue"
	></v-input>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';

export default {
	mixins: [mixin],
	methods: {
		emitValue(value) {
			if (value.length === 5) value = value + ':00';
			if (value.length === 0) value = null;

			this.$emit('input', value);
		}
	}
};
</script>

<style lang="scss" scoped></style>
