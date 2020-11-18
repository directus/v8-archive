<template>
	<select
		:id="name"
		:disabled="readonly"
		:size="options.size"
		class="select"
		multiple
		@change="updateValue($event.target.options)"
	>
		<option v-if="options.placeholder" value="" :disabled="required">
			{{ options.placeholder }}
		</option>
		<option
			v-for="(display, val) in choices"
			:key="val"
			:value="val"
			:selected="value && value.includes(val)"
		>
			{{ display }}
		</option>
	</select>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';

export default {
	mixins: [mixin],
	computed: {
		choices() {
			let choices = this.options.choices;

			if (!choices) return {};

			if (typeof this.options.choices === 'string') {
				choices = JSON.parse(this.options.choices);
			}

			return choices;
		}
	},
	methods: {
		updateValue(options) {
			let value = Array.from(options)
				.filter(input => input.selected && Boolean(input.value))
				.map(input => input.value)
				.join();

			if (value && this.options.wrapWithDelimiter) {
				value = `,${value},`;
			}

			value = value.split(',');
			this.$emit('input', value);
		}
	}
};
</script>

<style lang="scss" scoped>
.select {
	transition: all var(--fast) var(--transition);
	border: var(--input-border-width) solid var(--input-border-color);
	border-radius: var(--border-radius);
	width: 100%;
	max-width: var(--width-large);
	background-color: var(--input-background-color);
	font-size: var(--input-font-size);

	&:hover {
		transition: none;
		border-color: var(--input-border-color-hover);
	}
	&:focus {
		border-color: var(--input-border-color-focus);
	}
	option {
		transition: color var(--fast) var(--transition);
		padding: 5px 10px;
		&:hover {
			transition: none;
			background: var(--input-background-color-alt)
				linear-gradient(
					0deg,
					var(--input-background-color-alt) 0%,
					var(--input-background-color-alt) 100%
				);
		}
		&:checked {
			background: var(--input-background-color-active)
				linear-gradient(
					0deg,
					var(--input-background-color-active) 0%,
					var(--input-background-color-active) 100%
				);
			position: relative;
			color: var(--input-text-color-active);
			-webkit-text-fill-color: var(--input-text-color-active);

			&::after {
				content: 'check';
				font-family: 'Material Icons';
				font-size: 24px;
				position: absolute;
				right: 10px;
				top: 50%;
				transform: translateY(-54%);
				font-feature-settings: 'liga';
			}
		}
	}
}
</style>
