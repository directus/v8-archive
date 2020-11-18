<template>
	<transition name="slide">
		<div class="v-modal-base">
			<div class="mask" @click="$emit('cancel')" />
			<div class="wrapper">
				<aside class="modal">
					<h2 v-if="title" class="type-title">{{ title }}</h2>
					<p>{{ message }}</p>
					<slot />
				</aside>
			</div>
		</div>
	</transition>
</template>

<script>
export default {
	name: 'VModalBase',
	props: {
		title: {
			type: String,
			required: false
		},
		message: {
			type: String,
			required: false
		}
	},
	mounted() {
		this.$helpers.disableBodyScroll(this.$refs.modal);
	},
	beforeDestroy() {
		this.$helpers.enableBodyScroll(this.$refs.modal);
	}
};
</script>

<style lang="scss" scoped>
.v-modal-base {
	display: table;
	position: fixed;
	z-index: 500;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
}

.mask {
	position: absolute;
	left: 0;
	right: 0;
	top: 0;
	bottom: 0;
	background-color: var(--modal-smoke-color);
	opacity: 0.9;
	cursor: pointer;

	&.pointer {
		cursor: pointer;
	}
}

.wrapper {
	position: relative;
	display: table-cell;
	vertical-align: middle;
	pointer-events: none;
	opacity: 1;
	z-index: +1;
}

aside {
	position: relative;
	margin: 0 auto;
	width: 90%;
	max-width: 560px;
	background-color: var(--modal-background-color);
	border-radius: var(--border-radius);
	transition: inherit;
	pointer-events: painted;
	cursor: default;
	padding: 30px;
	overflow: auto;

	h2 {
		margin-bottom: 20px;
	}

	p {
		font-size: 16px;
		line-height: var(--line-height-more);
	}
}

.slide-enter-active,
.slide-enter-active aside {
	transition: var(--slow) var(--transition-in);
}

.slide-leave-active,
.slide-leave-active aside {
	transition: var(--medium) var(--transition-out);
}

.slide-enter,
.slide-leave-to {
	opacity: 0;
}
</style>
