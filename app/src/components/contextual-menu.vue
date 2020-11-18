<template>
	<v-popover :placement="placement" offset="2" :trigger="trigger" :disabled="disabled">
		<div class="menu-toggle" :class="{ disabled }">
			{{ text }}
			<v-icon v-if="icon" :name="icon" />
			<slot />
		</div>

		<template v-if="options !== null" slot="popover">
			<ul class="ctx-menu">
				<li v-for="(option, id) in options" :key="id">
					<button
						v-close-popover
						type="button"
						:class="{ disabled: option.disabled }"
						:disabled="option.disabled"
						@click.stop="optionClicked(option, id)"
					>
						<v-icon v-if="option.icon" :name="option.icon"></v-icon>
						{{ option.text }}
					</button>
				</li>
			</ul>
		</template>
	</v-popover>
</template>

<script>
export default {
	name: 'VContextualMenu',
	props: {
		text: {
			type: String,
			default: ''
		},
		icon: {
			type: String,
			default: 'more_vert'
		},
		options: {
			type: [Array, Object],
			default: null
		},
		trigger: {
			type: String,
			default: 'click'
		},
		placement: {
			type: String,
			default: 'left-start'
		},
		disabled: {
			type: Boolean,
			default: false
		}
	},
	computed: {
		body() {
			return document.body;
		}
	},
	methods: {
		optionClicked(option, id) {
			if (!option.disabled) this.$emit('click', id);
		}
	}
};
</script>

<style>
.v-popover .trigger {
	display: block !important;
}
</style>

<style lang="scss" scoped>
.menu-toggle {
	cursor: pointer;
	display: flex;
	align-items: center;

	i {
		color: var(--input-border-color);
		transition: color var(--fast) var(--transition);
	}

	&:hover i {
		color: var(--page-text-color);
		transition: none;
	}

	&.disabled {
		cursor: not-allowed;
		color: var(--input-background-color-disabled);
		&:hover i {
			color: var(--input-background-color-disabled);
		}
	}
}
.ctx-menu {
	list-style: none;
	padding: 4px;
	width: var(--width-small);
	li {
		display: block;
	}
	.v-icon {
		color: var(--popover-text-color);
		margin-right: 8px;
		transition: color var(--fast) var(--transition);
	}
	button {
		display: flex;
		align-items: center;
		padding: 4px 8px;
		color: var(--popover-text-color);
		width: 100%;
		height: 100%;
		transition: color var(--fast) var(--transition);

		&.disabled {
			color: var(--popover-text-color-disabled);
			cursor: not-allowed;
			i {
				color: var(--popover-text-color-disabled);
			}

			&:hover {
				background-color: transparent;
				color: var(--popover-text-color-disabled);
				i {
					color: var(--popover-text-color-disabled);
				}
			}
		}

		&:hover {
			color: var(--popover-text-color-hover);
			transition: none;
			i {
				color: var(--popover-text-color-hover);
				transition: none;
			}
		}
	}
}
</style>
