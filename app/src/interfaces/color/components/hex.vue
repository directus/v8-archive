<template>
	<div class="hex">
		<div class="color">
			<v-popover trigger="click" placement="bottom-start">
				<button
					class="trigger"
					:style="previewStyles"
					:disabled="readonly"
					:class="{ empty: !value }"
				></button>
				<chooser slot="popover" :value="value" @input="$emit('input', $event)" />
			</v-popover>
			<v-icon name="tune" color="--white" />
		</div>
		<v-input
			class="input"
			type="text"
			monospace
			:maxlength="7"
			placeholder="#000000"
			pattern="[#0-9a-fA-F]"
			:value="value"
			:disabled="readonly"
			@input="emitValue"
		/>
	</div>
</template>

<script>
import chooser from './chooser';

export default {
	components: {
		chooser
	},
	props: {
		value: {
			type: String,
			default: null
		},
		readonly: {
			type: Boolean,
			default: false
		}
	},
	data() {
		return {};
	},
	computed: {
		previewStyles() {
			return {
				backgroundColor: this.value
			};
		},
		body() {
			return document.body;
		}
	},
	methods: {
		emitValue(value) {
			if (/#[0-9A-Fa-f]{3,6}/.test(value)) {
				return this.$emit('input', value);
			}
		}
	}
};
</script>

<style lang="scss" scoped>
.hex {
	position: relative;
	min-height: var(--input-height);
}

.color {
	position: relative;
	top: 8px;
	left: 8px;
	z-index: 2;
	width: calc(var(--input-height) - 16px);
	height: calc(var(--input-height) - 16px);
	cursor: pointer;

	&:hover {
		.v-icon {
			opacity: 1;
		}
	}

	.trigger {
		display: block;
		border-radius: var(--border-radius);
		width: calc(var(--input-height) - 16px);
		height: calc(var(--input-height) - 16px);
		box-shadow: inset 0 0 0 2px rgba(0, 0, 0, 0.1);

		body.auto & {
			@media (prefers-color-scheme: dark) {
				box-shadow: inset 0 0 0 2px rgba(255, 255, 255, 0.2);
			}
		}

		body.dark & {
			box-shadow: inset 0 0 0 2px rgba(255, 255, 255, 0.2);
		}

		&.empty {
			box-shadow: inset 0 0 0 2px var(--input-border-color) !important;
			&::before,
			&::after {
				content: '';
				height: 2px;
				width: 125%;
				background-color: var(--input-border-color);
				position: absolute;
				top: calc(50% - 1px);
				left: -12.5%;
				transform: rotate(45deg);
			}
			&::after {
				transform: rotate(-45deg);
			}
		}
	}

	.v-icon {
		transition: opacity var(--fast) var(--transition);
		position: absolute;
		top: 6px;
		left: 6px;
		opacity: 0;
		pointer-events: none;
	}
}

.input {
	z-index: 1;
	width: 100%;
	max-width: var(--form-column-width);
	position: absolute;
	top: 0;
	left: 0;
}

// Once we have base components with proper slots, this can be discarded
.input ::v-deep input {
	padding-left: calc(var(--input-height));
}
</style>
