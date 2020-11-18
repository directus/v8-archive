<template>
	<div :class="{ 'icon-left': iconLeft, 'icon-right': iconRight }" class="v-input">
		<!-- Far from ideal, but it does the trick -->

		<input
			v-if="mask"
			:id="id"
			ref="input"
			v-mask="mask"
			v-focus="autofocus"
			:class="{ charactercount, monospace }"
			:type="type"
			:autocomplete="autocomplete"
			:max="max"
			:maxlength="maxlength"
			:min="min"
			:minlength="minlength"
			:name="name"
			:pattern="pattern"
			:placeholder="placeholder"
			:required="required"
			:readonly="readonly || disabled"
			:spellcheck="spellcheck"
			:value="value"
			:step="step"
			@keyup="$emit('keyup', $event)"
			@keydown="$emit('keydown', $event)"
			@input="$emit('input', $event.target.value)"
		/>

		<input
			v-else
			:id="id"
			ref="input"
			v-focus="autofocus"
			class="test"
			:class="{ charactercount, monospace }"
			:type="type"
			:autocomplete="autocomplete"
			:max="max"
			:maxlength="maxlength"
			:min="min"
			:minlength="minlength"
			:name="name"
			:pattern="pattern"
			:placeholder="placeholder"
			:required="required"
			:readonly="readonly || disabled"
			:spellcheck="spellcheck"
			:value="value"
			:step="step"
			@keyup="$emit('keyup', $event)"
			@keydown="$emit('keydown', $event)"
			@input="$emit('input', $event.target.value)"
		/>

		<v-icon
			v-if="iconLeft"
			v-tooltip="iconLeftTooltip"
			:name="iconLeft"
			:color="iconLeftColor"
			class="icon-left"
		/>
		<v-icon
			v-if="iconRight"
			v-tooltip="iconRightTooltip"
			:name="iconRight"
			:color="iconRightColor"
			class="icon-right"
		/>

		<span class="char-count" v-if="charactercount">{{ charsRemaining }}</span>
	</div>
</template>

<script>
export default {
	name: 'VInput',
	props: {
		type: {
			type: String,
			default: 'text'
		},
		autocomplete: {
			type: String,
			default: 'on'
		},
		autofocus: {
			type: Boolean,
			default: false
		},
		max: {
			type: [Number, Boolean, String],
			default: null
		},
		maxlength: {
			type: [Number, Boolean, String],
			default: null
		},
		min: {
			type: [Number, Boolean, String],
			default: null
		},
		minlength: {
			type: [Number, Boolean, String],
			default: null
		},
		name: {
			type: String,
			default: ''
		},
		pattern: {
			type: String,
			default: '.*'
		},
		placeholder: {
			type: String,
			default: ''
		},
		readonly: {
			type: Boolean,
			default: false
		},
		disabled: {
			type: Boolean,
			default: false
		},
		required: {
			type: Boolean,
			default: false
		},
		spellcheck: {
			type: Boolean,
			default: true
		},
		id: {
			type: String,
			default: ''
		},
		value: {
			type: null,
			default: ''
		},
		step: {
			type: [String, Number],
			default: 1
		},
		iconLeft: {
			type: String,
			default: ''
		},
		iconLeftColor: {
			type: String,
			default: '--input-icon-color'
		},
		iconLeftTooltip: {
			type: String,
			default: ''
		},
		iconRight: {
			type: String,
			default: ''
		},
		iconRightColor: {
			type: String,
			default: '--input-icon-color'
		},
		iconRightTooltip: {
			type: String,
			default: ''
		},
		valid: {
			type: Boolean,
			default: true
		},
		charactercount: {
			type: Boolean,
			default: false
		},
		mask: {
			type: [String, Array, Boolean],
			default: null
		},
		monospace: {
			type: Boolean,
			default: false
		}
	},
	computed: {
		charsRemaining() {
			if (!this.maxlength) return null;
			return this.maxlength - this.value.length;
		}
	}
};
</script>

<style lang="scss" scoped>
.v-input {
	position: relative;

	input {
		width: 100%;
		border: var(--input-border-width) solid var(--input-border-color);
		border-radius: var(--border-radius);
		color: var(--input-text-color);
		background-color: var(--input-background-color);
		text-transform: none;
		transition: var(--fast) var(--transition);
		transition-property: color, border-color, padding;
		height: var(--input-height);
		font-size: var(--input-font-size);
		padding: var(--input-padding);

		&.monospace {
			font-family: var(--family-monospace);
		}

		&[type='date'] {
			-webkit-appearance: none;
		}
		&[type='date']::-webkit-inner-spin-button {
			-webkit-appearance: none;
			display: none;
		}
		&::-webkit-clear-button {
			display: none; /* Hide the button */
			-webkit-appearance: none; /* turn off default browser styling */
		}

		&::placeholder {
			color: var(--input-placeholder-color);
		}

		&:hover:not(:read-only) {
			transition: none;
			border-color: var(--input-border-color-hover);
		}

		&:focus:not(:read-only) {
			border-color: var(--input-border-color-focus);
			outline: 0;
		}

		&:-webkit-autofill {
			background-color: var(--input-background-color) !important;
			box-shadow: inset 0 0 0 1000px var(--input-background-color) !important;
			color: var(--input-text-color) !important;
			-webkit-text-fill-color: var(--input-text-color) !important;
			font-size: var(--input-font-size);
		}

		&:-webkit-autofill,
		&:-webkit-autofill:hover,
		&:-webkit-autofill:focus {
			background-color: var(--input-background-color) !important;
			box-shadow: inset 0 0 0 2000px var(--input-background-color) !important;
			color: var(--input-text-color) !important;
			-webkit-text-fill-color: var(--input-text-color) !important;
			border: var(--input-border-width) solid var(--input-border-color) !important;
			font-size: var(--input-font-size) !important;
		}

		&:read-only {
			background-color: var(--input-background-color-disabled);
			border-color: var(--input-border-color);
			cursor: not-allowed;
			outline: none;
			&:focus {
				border-color: var(--input-border-color);
			}
		}
	}

	.char-count {
		position: absolute;
		right: 10px;
		top: 50%;
		transform: translateY(-50%);
		opacity: 0;
		transition: var(--fast) var(--transition);
		color: var(--input-icon-color);
		padding-left: 4px;
		background-color: var(--input-background-color);
	}

	input.charactercount:focus {
		padding-right: 30px;
	}

	input:not([readonly]):focus + span {
		opacity: 1;
	}

	&.icon-left input {
		padding-left: 38px;
	}

	&.icon-right input {
		padding-right: 38px;
	}

	&.icon-left .v-icon,
	&.icon-right .v-icon {
		position: absolute;
		top: 50%;
		color: var(--input-icon-color);
		transform: translateY(-50%);

		&.accent {
			color: var(--input-background-color-active);
		}

		&.success {
			color: var(--success);
		}

		&.warning {
			color: var(--warning);
		}

		&.danger {
			color: var(--danger);
		}
	}

	&.icon-left .v-icon.icon-left {
		left: 10px;
	}

	&.icon-right .v-icon.icon-right {
		right: 10px;
	}
}
</style>
