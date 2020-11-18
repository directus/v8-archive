<template>
	<div class="v-logo">
		<img v-if="customLogoPath" :src="customLogoPath" :alt="projectName" />
		<div v-else class="logo" :class="{ running }" @animationiteration="checkRunning" />
	</div>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
	name: 'VLogo',
	data() {
		return {
			running: false
		};
	},
	computed: {
		...mapGetters(['currentProject']),
		customLogoPath() {
			if (this.currentProject?.data?.project_logo) {
				return this.currentProject.data.project_logo.full_url;
			} else {
				return null;
			}
		},
		projectName() {
			return this.currentProject?.data.project_name;
		},
		queueContainsItems() {
			return this.$store.state.queue.length !== 0;
		}
	},
	watch: {
		queueContainsItems(newVal) {
			if (newVal === true) {
				this.running = true;
			}
		}
	},
	methods: {
		checkRunning() {
			if (this.queueContainsItems === false) {
				this.running = false;
			}
		}
	}
};
</script>

<style lang="scss" scoped>
.v-logo {
	background-color: var(--brand);

	display: flex;
	justify-content: center;
	align-items: center;
	position: relative;
	width: 64px;
	height: 64px;
	padding: 12px;

	> * {
		width: 100%;
		height: 100%;
		object-fit: contain;
	}

	.logo {
		background-image: url('../../../assets/sprite.svg');
		background-size: 600px 32px;
		background-position: 0% 0%;
		width: 40px;
		height: 32px;
		margin: 0 auto;
		position: absolute;
		top: 20px;
		left: 12px;
	}

	.running {
		animation: 560ms run steps(14) infinite;
	}
}

@keyframes run {
	100% {
		background-position: 100%;
	}
}
</style>
