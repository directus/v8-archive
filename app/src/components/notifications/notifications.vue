<template>
	<div class="notifications">
		<transition-group name="slide-fade" tag="div">
			<v-item
				v-for="notification in notifications"
				:key="notification.id"
				:item="notification"
			/>
		</transition-group>
	</div>
</template>
<script>
import VItem from './item.vue';
export default {
	name: 'VNotifications',
	components: {
		VItem
	},
	computed: {
		notifications() {
			return this.$store.state.notifications.queue;
		}
	}
};
</script>

<style lang="scss" scoped>
.notifications {
	position: fixed;
	bottom: 0;
	z-index: 999;
	width: 100%;
	left: 0;
	right: 0;
	padding: 0 8px;

	@media (min-width: 800px) {
		width: 300px;
		right: 32px;
		bottom: 20px;
		left: auto;
		padding: 0;
	}
}

.slide-fade-enter-active,
.slide-fade-move {
	transition: all var(--medium) ease-out;
}
.slide-fade-leave-active {
	transition: all var(--slow) cubic-bezier(1, 0.5, 0.8, 1);
	/* position: absolute; */
}
.slide-fade-enter,
.slide-fade-leave-to {
	transform: translateX(60px);
	max-height: 0;
	opacity: 0;
}
</style>
