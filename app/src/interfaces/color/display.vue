<template>
	<div v-tooltip="value" class="swatch" :style="{ backgroundColor: value }"></div>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';

export default {
	mixins: [mixin]
};
</script>

<style lang="scss" scoped>
.swatch {
	transition: var(--fast) var(--transition);
	display: inline-block;
	width: 24px;
	height: 24px;
	border-radius: 100%;
	vertical-align: middle;
	margin-right: 4px;
	color: var(--white);
	text-align: center;
}
</style>
