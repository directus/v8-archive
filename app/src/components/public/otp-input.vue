<template>
	<the-mask
		v-focus
		type="text"
		class="otp-input"
		:value="value"
		mask="###-###"
		placeholder="***-***"
		:masked="false"
		@input="onInput"
	/>
</template>

<script>
export default {
	name: 'OTPInput',
	props: {
		value: {
			type: String,
			default: ''
		}
	},
	methods: {
		onInput(value) {
			if (value.length === 0 || value.length === 6) {
				this.$emit('input', value);
			}
		}
	}
};
</script>

<style lang="scss" scoped>
.otp-input {
	position: relative;
	width: 100%;
	margin-bottom: 32px;
	border: 0;
	font-size: 22px;
	border: var(--input-border-width) solid var(--input-border-color);
	width: 100%;
	height: 64px;
	padding: 20px 10px;
	background-color: var(--input-background-color);
	color: var(--darker-gray);
	transition: border-color var(--fast) var(--transition);
	border-radius: var(--border-radius);
	font-family: var(--family-monospace);
	text-align: center;

	&::placeholder {
		color: var(--light-gray);
	}

	&:-webkit-autofill {
		color: var(--darker-gray) !important;
		-webkit-text-fill-color: var(--darker-gray);
		-webkit-box-shadow: 0 0 0px 1000px var(--white) inset;
	}

	&:hover:not([disabled]) {
		transition: none;
		border-color: var(--gray);
		&:focus {
			border-color: var(--darker-gray);
		}
	}

	&[disabled] {
		cursor: not-allowed;
		background-color: var(--input-background-color-disabled);
	}

	&:focus {
		outline: 0;
		border-color: var(--darker-gray);

		&:-webkit-autofill {
			color: var(--darker-gray) !important;
			-webkit-text-fill-color: var(--darker-gray);
			-webkit-box-shadow: 0 0 0px 1000px var(--white) inset;
		}
	}
}
</style>
