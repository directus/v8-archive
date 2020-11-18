<template>
	<div class="public-stepper">
		<div class="progress-line" :style="lineStyles"></div>
		<span
			v-for="step in steps"
			:key="step"
			:class="{
				current: step === currentStep,
				done: currentStep > step
			}"
			class="step"
		>
			<v-icon v-if="currentStep > step" color="--white" small name="check" />
		</span>
	</div>
</template>

<script>
export default {
	name: 'PublicStepper',
	props: {
		currentStep: {
			type: Number,
			default: 1
		},
		steps: {
			type: Number,
			default: 1
		}
	},
	computed: {
		lineStyles() {
			return {
				transform: `scaleX(${(1 / (this.steps - 1)) *
					((this.currentStep > this.steps ? this.steps : this.currentStep) - 1)})`
			};
		}
	}
};
</script>

<style lang="scss" scoped>
.public-stepper {
	width: 100%;
	display: flex;
	position: relative;
	align-items: center;
	justify-content: space-between;
	margin-left: 8px; // Compensate for box-shadow
	margin-right: 8px;

	&::before,
	.progress-line {
		// line
		content: '';
		position: absolute;
		display: block;
		height: 2px;
		width: 100%;
		left: 0;
		top: calc(50% - 1px);
		background-color: var(--blue-grey-100);
		z-index: 1;
	}

	.progress-line {
		background-color: var(--blue-grey-800);
		transform-origin: left;
	}

	.step {
		display: inline-block;
		width: 8px;
		height: 8px;
		box-shadow: 0 0 0 4px var(--white), 0 0 0 6px var(--blue-grey-100);
		background-color: var(--white);
		border-radius: 50%;
		position: relative;
		z-index: 2;

		&.current {
			box-shadow: 0 0 0 4px var(--white), 0 0 0 6px var(--blue-grey-800);
			background-color: var(--blue-grey-800);
		}

		&.done {
			width: 20px;
			height: 20px;
			box-shadow: none;
			background-color: var(--blue-grey-800);
			display: flex;
			justify-content: center;
			align-items: center;
		}
	}
}
</style>
