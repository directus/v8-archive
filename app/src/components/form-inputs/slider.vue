<template>
	<div class="v-slider">
		<div class="input">
			<input
				:id="id"
				:name="id"
				:value="value"
				:min="min"
				:max="max"
				:step="step"
				type="range"
				@input="$emit('input', Number($event.target.value))"
			/>
			<div ref="track" class="track-wrapper">
				<div :style="{ width: progress * 100 + '%' }" class="track-fill" />
			</div>
			<div class="output-container">
				<output
					:for="id"
					:class="{
						'output-shown': alwaysShowOutput
					}"
					:style="{ left: progress * 100 + '%' }"
				>
					{{ valueOrDefault }} {{ unit }}
				</output>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'VSlider',
	props: {
		id: {
			type: String,
			default: null
		},
		value: {
			type: Number,
			default: null
		},
		min: {
			type: Number,
			default: 0
		},
		max: {
			type: Number,
			default: 100
		},
		step: {
			type: [Number, String],
			default: 1
		},
		unit: {
			type: String,
			default: null
		},
		alwaysShowOutput: {
			type: Boolean,
			default: false
		}
	},
	data() {
		return {
			valuePos: 0
		};
	},
	computed: {
		valueOrDefault() {
			if (this.value == null) {
				return (this.max - this.min) / 2;
			}

			return this.value;
		},
		progress() {
			return (
				Math.round(((this.valueOrDefault - this.min) / (this.max - this.min)) * 100) / 100
			);
		}
	}
};
</script>

<style lang="scss" scoped>
.v-slider {
	position: relative;

	.input {
		transition: border-color var(--fast) var(--transition);
		padding: 12px 0 0;
		border-radius: 2px;

		flex-grow: 1;
		flex-shrink: 0;
		display: grid;
		grid-template-columns: 2;
		grid-template-rows: 1;
		grid-gap: 10px;

		&:hover {
			transition: none;
			border-color: var(--blue-grey-300);
		}
	}

	.track-wrapper {
		height: 2px;
		width: 100%;
		border-radius: 2px;
		pointer-events: none;
		user-select: none;
		position: relative;
		background-color: var(--input-border-color);

		.track-fill {
			position: absolute;
			background-color: var(--input-text-color);
			border-radius: 2px;
			width: 100%;
			height: 100%;
			left: 0;
			width: 0;
			z-index: 2;
		}
	}

	input,
	.track-wrapper {
		grid-row: 1;
		grid-column: 1;
	}

	input {
		appearance: none;
		-webkit-appearance: none;
		width: 100%;
		height: 2px;

		&::-webkit-slider-runnable-track {
			height: 2px;
			background: var(--input-border-color);
			box-shadow: none;
			border: none;
			border-radius: 2px;
		}

		&::-webkit-slider-thumb {
			appearance: none;
			-webkit-appearance: none;
			box-shadow: none;
			border: none;
			height: 14px;
			width: 14px;
			border-radius: 50%;
			background: var(--input-text-color);
			margin-top: -6px;
			cursor: ew-resize;
			box-shadow: 0 0 0 4px var(--input-background-color);
			z-index: +1;
			position: relative;
		}

		&::-moz-range-thumb {
			appearance: none;
			-webkit-appearance: none;
			box-shadow: none;
			border: none;
			height: 14px;
			width: 14px;
			border-radius: 50%;
			background: var(--input-text-color);
			margin-top: -6px;
			cursor: ew-resize;
			box-shadow: 0 0 0 2px var(--input-background-color);
			z-index: +1;
			position: relative;
		}

		&::-moz-focus-outer {
			border: 0;
		}
	}
	.output-container {
		position: relative;
		margin-right: 14px;
		margin-top: 2px;
		height: 0;
		pointer-events: none;
	}
	output {
		transition: opacity var(--fast) var(--transition);
		position: absolute;
		display: inline-block;
		white-space: nowrap;
		top: 4px;
		margin-left: 7px;
		text-align: center;
		transform: translateX(-50%);
		color: var(--input-text-color);
		background-color: var(--input-background-color-alt);
		border-radius: var(--border-radius);
		font-size: var(--input-font-size);
		padding: 4px 8px;
		opacity: 0;
		user-select: none;
		z-index: 4;
		&:before {
			content: '';
			position: absolute;
			top: -4px;
			left: calc(50%);
			width: 10px;
			height: 10px;
			border-radius: var(--border-radius);
			transform: translateX(-50%) rotate(45deg);
			background-color: var(--input-background-color-alt);
		}
		&.output-shown {
			opacity: 1;
		}
	}

	.input:hover output,
	.input:active output,
	.user-is-tabbing .input:focus output,
	.user-is-tabbing .input:focus-within output {
		opacity: 1;
		transition: var(--fast) var(--transition-in);
	}
}
</style>
