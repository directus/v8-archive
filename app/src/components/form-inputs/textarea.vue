<template>
	<textarea
		:id="id"
		:cols="cols"
		:disabled="disabled"
		:maxlength="maxlength"
		:minlength="minlength"
		:name="name"
		:placeholder="placeholder"
		:readonly="readonly"
		:required="required"
		:rows="rows"
		:spellcheck="spellcheck"
		:wrap="wrap"
		:value="value"
		:autofocus="autofocus"
		:class="[serif ? 'type-body-serif' : 'type-body-sans']"
		class="v-textarea"
		@keydown="$emit('keydown', $event.target.value)"
		@input="$emit('input', $event.target.value)"
	/>
</template>

<script>
export default {
	name: 'VTextarea',
	props: {
		cols: {
			type: Number,
			default: 20
		},
		disabled: {
			type: Boolean,
			default: false
		},
		maxlength: {
			type: Number,
			default: null
		},
		minlength: {
			type: Number,
			default: null
		},
		name: {
			type: String,
			default: null
		},
		id: {
			type: String,
			default: null
		},
		placeholder: {
			type: String,
			default: null
		},
		readonly: {
			type: Boolean,
			default: false
		},
		required: {
			type: Boolean,
			default: false
		},
		rows: {
			type: Number,
			default: null
		},
		serif: {
			type: Boolean,
			default: false
		},
		spellcheck: {
			type: Boolean,
			default: null
		},
		wrap: {
			type: String,
			default: null
		},
		value: {
			type: String,
			default: null
		},
		autofocus: {
			type: String,
			default: null
		}
	}
};
</script>

<style lang="scss" scoped>
textarea {
	width: 100%;
	border: var(--input-border-width) solid var(--input-border-color);
	border-radius: var(--border-radius);
	color: var(--input-text-color);
	font-size: var(--input-font-size);
	background-color: var(--input-background-color);
	transition: var(--fast) var(--transition);
	transition-property: color, border-color;

	&.type-body-serif {
		padding: 20px;
	}

	&.type-body-sans {
		padding: 10px;
	}

	&::placeholder {
		color: var(--input-placeholder-color);
	}

	&:read-only {
		background-color: var(--input-background-color-disabled);
		cursor: not-allowed;
	}

	&:hover:not(:read-only) {
		transition: none;
		border-color: var(--input-border-color-hover);
		outline: 0;
	}

	&:focus:not(:read-only) {
		border-color: var(--input-border-color-focus);
		outline: 0;
	}

	&:focus:not(:read-only) + i {
		color: var(--input-text-color);
	}

	&:-webkit-autofill {
		box-shadow: inset 0 0 0 1000px var(--white) !important;
		color: var(--input-text-color) !important;
		-webkit-text-fill-color: var(--input-text-color) !important;
	}

	input:-webkit-autofill,
	input:-webkit-autofill:hover,
	input:-webkit-autofill:focus input:-webkit-autofill,
	textarea:-webkit-autofill,
	textarea:-webkit-autofill:hover textarea:-webkit-autofill:focus {
		border: var(--input-border-width) solid var(--input-border-color);
		background-color: var(--white);
		box-shadow: inset 0 0 0 2000px var(--white);
	}
}
</style>
