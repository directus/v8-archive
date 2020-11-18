<template>
	<v-input
		:id="name"
		type="date"
		class="interface-date"
		:name="name"
		:min="options.min"
		:max="options.max"
		:icon-left="options.iconLeft"
		:icon-right="options.iconRight"
		:readonly="readonly"
		:value="value"
		@input="$emit('input', $event)"
	></v-input>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';

export default {
	name: 'InterfaceDate',
	mixins: [mixin]
};
</script>

<style lang="scss" scoped>
.interface-date {
	width: 100%;
}
</style>
