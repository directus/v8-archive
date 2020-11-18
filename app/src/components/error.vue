<template>
	<transition name="error">
		<div class="v-error">
			<div class="circle" :style="{ borderColor: 'var(--' + color + ')' }">
				<v-icon class="icon" :class="icon" :name="icon" x-large :color="`--${color}`" />
			</div>
			<h2 class="type-heading-small" :style="{ color: 'var(--' + color + ')' }">
				{{ title }}
			</h2>
			<p>{{ body }}</p>
		</div>
	</transition>
</template>

<script>
export default {
	name: 'VError',
	props: {
		color: {
			type: String,
			default: 'page-text-color',
			validator(val) {
				return ['page-text-color', 'accent', 'success', 'warning', 'danger'].includes(val);
			}
		},
		icon: {
			type: String,
			required: true
		},
		title: {
			type: String,
			required: true
		},
		body: {
			type: String,
			required: true
		}
	}
};
</script>

<style lang="scss" scoped>
.v-error {
	display: flex;
	justify-content: center;
	align-items: center;
	flex-direction: column;
	margin: 100px 0;

	.circle {
		border: 2px solid var(--blue-grey-50);
		border-radius: 50%;
		padding: 20px;
		width: 88px;
		height: 88px;
		display: flex;
		align-items: center;
		justify-content: center;
		.icon {
			// optical alignment for weighted icons
			&.warning {
				margin-top: -4px;
			}
		}
	}

	h2 {
		margin-top: 12px;
		margin-bottom: 8px;
	}

	p {
		color: var(--note-text-color);
		max-width: 200px;
		text-align: center;
	}
}

.error-enter-active {
	transition: var(--slow) var(--transition-in);

	> * {
		transition: var(--slow) var(--transition-in);

		&:nth-child(2) {
			transition-delay: 50ms;
		}

		&:nth-child(3) {
			transition-delay: 100ms;
		}
	}
}

.error-enter {
	> * {
		opacity: 0;
		transform: translateY(15px);
	}
}
</style>
