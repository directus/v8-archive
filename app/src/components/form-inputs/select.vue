<template>
	<div :class="{ icon }" class="v-select">
		<select
			v-if="other"
			:id="otherActive ? null : id"
			:disabled="disabled || readonly"
			:value="value"
			@change="change($event.target.value)"
		>
			<optgroup :label="$t('values')">
				<option v-for="(key, value) in parsedOptions" :key="value" :value="value">
					{{ key }}
				</option>
			</optgroup>
			<optgroup :label="$t('other')">
				<option :value="customValue || '__other'" :selected="otherActive">
					{{ customValue.length ? customValue : $t('enter_value') }}
				</option>
			</optgroup>
		</select>
		<select
			v-else
			:id="otherActive ? null : id"
			ref="select"
			:disabled="disabled || readonly"
			:value="value"
			@change="change($event.target.value)"
		>
			<option v-if="placeholder" ref="default" selected disabled value="">
				{{ placeholder }}
			</option>
			<option
				v-for="(key, optionValue) in parsedOptions"
				:key="optionValue"
				:value="optionValue"
				:selected="value == optionValue"
			>
				{{ key }}
			</option>
		</select>
		<input
			v-if="otherActive"
			:id="id"
			ref="input"
			v-focus
			:type="type"
			:value="customValue"
			:placeholder="placeholder"
			autofocus
			@input="changeCustom"
		/>
		<div class="value">
			<v-icon v-if="icon" :name="icon" />
			<span v-if="placeholder && !value" class="placeholder">{{ placeholder }}</span>
			<span v-if="parsedOptions[value]" class="no-wrap">{{ parsedOptions[value] }}</span>
			<span v-else class="no-wrap">{{ value }}</span>
		</div>
		<v-icon class="chevron" name="arrow_drop_down" />
	</div>
</template>

<script>
export default {
	name: 'VSelect',
	props: {
		disabled: {
			type: Boolean,
			default: false
		},
		readonly: {
			type: Boolean,
			default: false
		},
		name: {
			type: String,
			default: ''
		},
		id: {
			type: String,
			default: ''
		},
		value: {
			type: [String, Number],
			default: ''
		},

		other: {
			type: Boolean,
			default: false
		},
		icon: {
			type: String,
			default: ''
		},
		type: {
			type: String,
			default: ''
		},
		options: {
			type: [Object, String, Array],
			required: true
		},
		placeholder: {
			type: String,
			default: 'Choose one...'
		},

		defaultValue: {
			type: Boolean,
			default: false
		}
	},
	data() {
		return {
			otherActive: false,
			customValue: ''
		};
	},
	computed: {
		parsedOptions() {
			if (typeof this.options === 'string') {
				return JSON.parse(this.options);
			}

			return this.options;
		}
	},
	methods: {
		change(value) {
			if (value === this.customValue || value === '__other') {
				this.$emit('input', this.customValue);
				this.otherActive = true;
				return;
			}

			this.otherActive = false;
			this.$emit('input', value);

			if (this.defaultValue === true) {
				this.$refs.default.setAttribute('selected', 'selected');
				this.$refs.select.value = '';
			}
		},
		changeCustom(event) {
			this.customValue = event.target.value;
			this.$emit('input', this.customValue);
		}
	}
};
</script>

<style lang="scss" scoped>
.v-select {
	position: relative;

	select {
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		opacity: 0;
		cursor: pointer;
		appearance: none;
	}

	.value,
	input {
		transition: all var(--fast) var(--transition);
		background-color: var(--white);
		color: var(--input-text-color);
		background-color: var(--input-background-color);
		height: var(--input-height);
		border: var(--input-border-width) solid var(--input-border-color);
		transition: var(--fast) var(--transition);
		transition-property: color, border-color;
		font-size: var(--input-font-size);

		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		display: inline-block;

		span.no-wrap {
			padding-right: 18px;
		}
	}

	.value {
		width: 100%;
		display: flex;
		align-items: center;
		padding: 10px;
		border-radius: var(--border-radius);
		position: relative;
		user-select: none;
		pointer-events: none;
	}

	input {
		position: absolute;
		left: 12px;
		height: 100%;
		top: 0;
		width: calc(100% - 40px);
		border-left: 0;
		border-right: 0;
		z-index: +1;

		&::placeholder {
			color: var(--input-placeholder-color);
		}

		&:-webkit-autofill {
			box-shadow: inset 0 0 0 1000px var(--white) !important;
		}
	}

	select:disabled,
	input:disabled {
		cursor: not-allowed;
	}

	select:disabled ~ div,
	select:disabled ~ input,
	input:disabled + div,
	input:disabled {
		background-color: var(--input-background-color-disabled);
		cursor: not-allowed;
	}

	select:hover:not(:disabled) ~ div,
	select:hover:not(:disabled) ~ input,
	input:hover:not(:disabled) + div,
	input:hover:not(:disabled) {
		border-color: var(--input-border-color-hover);
		transition: none;
	}

	select:focus ~ div,
	select:focus ~ input,
	input:focus + div,
	input:focus,
	select:hover:not(:disabled):focus ~ div,
	select:hover:not(:disabled):focus ~ input,
	input:hover:not(:disabled):focus + div,
	input:hover:not(:disabled):focus {
		border-color: var(--input-border-color-focus);
		outline: 0;
	}

	.v-icon {
		position: absolute;
		left: 5px;
		top: 50%;
		color: var(--input-icon-color);
		transform: translateY(-50%);
	}

	.v-icon.chevron {
		left: auto;
		pointer-events: none;
		right: 10px;
	}

	&.icon {
		.value {
			padding-left: 38px;
		}

		input {
			width: calc(100% - 35px - 40px);
			left: 35px;
		}

		select:focus ~ div i,
		input:focus + div i,
		input:focus i {
			color: var(--blue-grey-300);
		}
	}

	.placeholder {
		color: var(--input-placeholder-color);
		width: 100%;
		text-overflow: ellipsis;
		overflow: hidden;
	}
}
</style>
