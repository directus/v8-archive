<template>
	<transition name="fade">
		<div v-show="active" :class="area" :style="{ zIndex }" class="loader">
			<div :class="{ transparent }" class="overlay" />
			<transition name="fade">
				<v-spinner v-show="spinnerActive" class="spinner" />
			</transition>
		</div>
	</transition>
</template>

<script>
export default {
	name: 'Loader',
	props: {
		area: {
			type: String,
			default: null,
			validator(val) {
				const validAreas = ['content', 'full-page'];
				return validAreas.includes(val);
			}
		},
		transparent: {
			type: Boolean,
			default: false
		},
		delay: {
			type: Number,
			default: 0
		},
		spinnerDelay: {
			type: Number,
			default: 0
		},
		zIndex: {
			type: Number,
			default: 500
		}
	},
	data() {
		return {
			active: false,
			spinnerActive: false
		};
	},
	created() {
		setTimeout(() => {
			this.active = true;
		}, this.delay);

		setTimeout(() => {
			this.spinnerActive = true;
		}, this.delay + this.spinnerDelay);
	}
};
</script>

<style lang="scss" scoped>
.loader {
	position: absolute;
	width: 100%;
	height: 100%;
	right: 0;
	bottom: 0;
}

.overlay {
	position: absolute;
	width: 100%;
	height: 100%;
	right: 0;
	bottom: 0;
	pointer-events: none;
	user-select: none;

	&.transparent {
		opacity: 0.7;
	}
}

.spinner {
	position: absolute;
	left: 50%;
	top: 45%;
	transform: translate(-50%, -50%);
}

.loader.full-page {
	position: fixed;
	width: 100%;
	height: 100%;
	top: 0;
	left: 0;
}

.loader.content {
	position: fixed;
	width: 100%;
	height: calc(100% - var(--header-height));

	@media (min-width: 800px) {
		width: calc(100% - var(--nav-sidebar-width));
	}
}

.fade-enter-active,
.fade-leave-active {
	transition: opacity var(--medium) var(--transition-in);
}

.fade-enter,
.fade-leave-to {
	opacity: 0;
}
</style>
