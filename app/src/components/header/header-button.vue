<template>
	<div
		v-tooltip="{
			delay: { show: 750, hide: 100 },
			content: label
		}"
		class="v-header-button"
	>
		<v-contextual-menu
			v-if="Object.keys(options).length > 0"
			class="options"
			placement="bottom-end"
			:options="options"
			@click="emitChange"
		></v-contextual-menu>
		<component
			:is="disabled ? 'button' : to ? 'router-link' : 'button'"
			:style="{
				backgroundColor: outline || disabled ? null : `var(--${backgroundColor})`,
				borderColor: `var(--${backgroundColor})`,
				color: `var(--${backgroundColor})`,
				'--hover-color': hoverColor ? `var(--${hoverColor})` : null
			}"
			:class="{ attention: alert, outline: outline, 'has-bg': hoverColor }"
			class="button"
			:disabled="disabled || loading"
			:to="to || null"
			@click="!to ? $emit('click', $event) : null"
		>
			<v-spinner v-if="loading" :size="24" color="white" background-color="transparent" />
			<v-icon v-else :color="`--${iconColor}`" :name="icon" />
		</component>
	</div>
</template>

<script>
export default {
	name: 'VHeaderButton',
	props: {
		icon: {
			type: String,
			required: true
		},
		backgroundColor: {
			type: String,
			default: 'button-secondary-background-color'
		},
		hoverColor: {
			type: String,
			default: null
		},
		iconColor: {
			type: String,
			default: 'button-secondary-text-color'
		},
		disabled: {
			type: Boolean,
			default: false
		},
		loading: {
			type: Boolean,
			default: false
		},
		options: {
			type: Object,
			default: () => ({})
		},
		alert: {
			type: Boolean,
			default: false
		},
		// Outline should be used for navigation (non-action) only
		outline: {
			type: Boolean,
			default: false
		},
		to: {
			type: String,
			default: null
		},
		label: {
			type: String,
			default: undefined
		}
	},
	data() {
		return {
			choice: null
		};
	},
	methods: {
		emitChange(event) {
			this.$emit('input', event);
			this.choice = null;
		}
	}
};
</script>

<style scoped lang="scss">
.v-header-button {
	position: relative;
	height: calc(var(--header-height) - 20px);
	width: calc(var(--header-height) - 20px);
	min-width: calc(var(--header-height) - 20px);
	display: inline-block;
	margin-left: 12px;
}

.button {
	transition: background-color var(--fast) var(--transition);
}

.button.has-bg:hover:not([disabled]) {
	background-color: var(--hover-color) !important;
}

button,
a {
	position: relative;
	background-color: transparent;
	border: 0;
	display: flex;
	justify-content: center;
	align-items: center;
	height: 100%;
	width: 100%;
	border-radius: 100%;
	overflow: hidden;
	cursor: pointer;
	text-decoration: none;

	&.outline {
		border: 2px solid var(--button-secondary-background-color);
		background-color: transparent;
		i {
			color: var(--button-secondary-background-color);
		}
	}

	i {
		transition: 100ms var(--transition);
		color: var(--white);
	}

	&:not([disabled]):active i {
		transform: scale(0.916);
	}

	&::after {
		content: '';
		display: block;
		width: 10px;
		height: 10px;
		background-color: var(--warning);
		border-radius: 50%;
		position: absolute;
		top: 27%;
		right: 27%;
		border: 2px solid currentColor;
		transform: scale(0);
		transition: transform var(--fast) var(--transition-out);
	}

	&.attention::after {
		transform: scale(1);
		transition: transform var(--fast) var(--transition-in);
	}
}

button[disabled] {
	background-color: var(--input-background-color-disabled) !important;
	cursor: not-allowed;
	i {
		color: var(--input-text-color) !important;
	}
}

.options {
	display: flex;
	justify-content: center;
	align-items: center;
	height: 100%;
	position: absolute;
	overflow: hidden;
	right: -20px;
	z-index: +1;
}
</style>
