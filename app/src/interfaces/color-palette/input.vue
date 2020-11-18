<template>
	<div class="interface-color-picker">
		<button
			v-for="color in colors"
			:key="color"
			v-tooltip="$helpers.formatTitle(color)"
			:disabled="readonly"
			:style="{ backgroundColor: `var(--${color})` }"
			:class="{ active: value === color }"
			@click="$emit('input', color)"
		>
			<template v-if="value === color">
				<v-icon name="check" :class="{ dark: useDarkIconColor(color) }" small />
			</template>
		</button>
	</div>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';
import hexRgb from 'hex-rgb';
import colors from './colors.json';

export default {
	mixins: [mixin],
	computed: {
		colors() {
			return colors;
		}
	},
	methods: {
		useDarkIconColor(value) {
			const hex = getComputedStyle(document.body)
				.getPropertyValue(`--${value}`)
				.trim();

			const rgb = hexRgb(hex, { format: 'array' });

			const colors = rgb
				.map(val => val / 255)
				.map(val => {
					if (val <= 0.03928) {
						return val / 12.92;
					}

					return Math.pow((val + 0.055) / 1.055, 2.4);
				});

			const lightness = 0.2126 * colors[0] + 0.7152 * colors[1] + 0.0722 * colors[2];

			return lightness > 0.25;
		}
	}
};
</script>

<style lang="scss" scoped>
.interface-color-picker {
	--swatch-size: 25px;

	position: relative;
	display: flex;
	flex-direction: column;
	flex-wrap: wrap;
	height: calc(var(--swatch-size) * 11);
	margin-bottom: calc(var(--swatch-size) + var(--input-note-margin));

	button {
		flex-basis: var(--swatch-size);
		height: var(--swatch-size);
		display: block;
		transform: scale(1);
		transition: transform var(--fast) var(--transition-in);

		&:hover {
			transition: none;
			transform: scale(1.4);
			z-index: +1;
			box-shadow: var(--box-shadow);
		}

		i {
			position: absolute;
			left: 0;
			right: 0;
			bottom: 0;
			top: 3px;
			margin: auto;
			color: var(--white);

			&.dark {
				color: var(--black);
			}
		}

		&:nth-last-child(1) {
			flex-grow: 1;

			position: absolute;
			left: 50%;
			top: calc(var(--swatch-size) * 11);
			width: 50%;

			&:hover {
				transform: scale(1.1);
			}
		}

		&:nth-last-child(2) {
			flex-grow: 1;
			border: 1px solid var(--blue-grey-50);

			position: absolute;
			left: 0;
			top: calc(var(--swatch-size) * 11);
			width: 50%;

			&:hover {
				transform: scale(1.1);
			}
		}
	}
	button[disabled='disabled'] {
		cursor: not-allowed;
		filter: grayscale(1);

		&.active {
			filter: none;
		}

		&:hover {
			transform: none;
			transition: none;
		}
	}
}
</style>
