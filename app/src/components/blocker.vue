<template>
	<transition name="fade">
		<div
			:style="{ zIndex }"
			class="blocker"
			@click="$emit('click')"
			@touchend="$emit('click')"
		/>
	</transition>
</template>

<script>
export default {
	name: 'Blocker',
	props: {
		zIndex: {
			type: Number,
			default: 0
		}
	}
};
</script>

<style scoped>
.blocker {
	position: fixed;
	width: 100%;
	height: 100%;
	left: 0;
	top: 0;
	background-color: var(--blue-grey-900);
	opacity: 0.8;
	cursor: pointer;
}

.fade-enter-active {
	transition: var(--slow) var(--transition-in);
}

.fade-leave-active {
	transition: var(--medium) var(--transition-out);
}

.fade-enter,
.fade-leave-to {
	opacity: 0;
}
</style>
