<template>
	<div
		class="v-progress-ring"
		:style="{
			height: radius * 2 + 'px',
			width: radius * 2 + 'px',
			minWidth: radius * 2 + 'px'
		}"
	>
		<svg>
			<circle
				class="background"
				:fill="`var(--${color})`"
				stroke="transparent"
				:stroke-width="stroke"
				:r="normalizedRadius"
				:cx="radius"
				:cy="radius"
			/>
			<circle
				:stroke="`var(--${color})`"
				fill="transparent"
				:stroke-dasharray="circumference + ' ' + circumference"
				:style="{ strokeDashoffset: strokeDashoffset }"
				:stroke-width="stroke"
				:r="normalizedRadius"
				:cx="radius"
				:cy="radius"
			/>
		</svg>
		<v-icon v-if="icon" :size="iconSize" :color="`--${color}`" :name="icon" />
	</div>
</template>

<script>
export default {
	name: 'VProgressRing',
	props: {
		radius: {
			type: Number,
			default: 24
		},
		stroke: {
			type: Number,
			default: 2
		},
		progress: {
			type: Number,
			required: true,
			validator(val) {
				return val >= 0 && val <= 100;
			}
		},
		color: {
			type: String,
			default: 'blue-grey-600'
		},
		icon: {
			type: String,
			default: null
		}
	},
	data() {
		const normalizedRadius = this.radius - this.stroke * 2;
		const circumference = normalizedRadius * 2 * Math.PI;

		return {
			normalizedRadius,
			circumference
		};
	},
	computed: {
		strokeDashoffset() {
			return this.circumference - (this.progress / 100) * this.circumference;
		},
		iconSize() {
			// Material design icons should be rendered in increments of 6 for visual clarity
			return 6 * Math.round(this.radius / 6);
		}
	}
};
</script>

<style lang="scss" scoped>
.v-progress-ring {
	position: relative;
	text-align: center;

	svg {
		width: 100%;
		height: 100%;
		transform: rotate(-90deg);
	}

	.background {
		opacity: 0.2;
	}

	> *:not(svg) {
		position: absolute;
		top: 50%;
		left: 0;
		right: 0;
		margin: 0 auto;
		width: max-content;
		transform: translateY(-50%);
	}
}
</style>
