<template>
	<component
		:is="element"
		:class="{
			link,
			selected,
			selectable,
			'selection-mode': selectionMode,
			disabled,
			'text-background': textBackground
		}"
		class="v-card"
		@click="$emit('click', $event)"
	>
		<component
			:is="wrapperTag"
			:to="to"
			:href="href"
			target="__blank"
			:class="{
				'only-show-on-hover': onlyShowOnHover
			}"
		>
			<div
				v-if="src || icon || $slots.icon"
				:style="{ backgroundColor: `var(--${color})` }"
				class="header"
				:class="{
					'big-image': bigImage && src && !error,
					'medium-image': mediumImage && src && !error
				}"
			>
				<button
					v-if="selectable"
					type="button"
					class="select"
					@click.stop="$emit('select')"
				>
					<v-icon :name="selectionIcon" />
				</button>

				<img v-if="src && !error" :alt="title" :src="src" @error="onImageError" />

				<v-icon v-if="error" class="error icon" name="broken_image" x-large color="white" />

				<v-icon
					v-if="icon"
					:class="{ 'half-opacity': opacity === 'half' }"
					class="icon"
					:name="icon"
					x-large
					color="white"
				/>

				<div v-if="$slots.icon" class="custom-icon"><slot name="icon" /></div>

				<span v-if="label" class="label">{{ label }}</span>
			</div>
			<div v-else class="header small" :style="{ backgroundColor: `var(--${color})` }">
				<button
					v-if="selectable"
					type="button"
					class="select"
					@click.stop="$emit('select')"
				>
					<v-icon :name="selectionIcon" />
				</button>
			</div>
			<div class="body" :class="{ menu: options != null }">
				<div class="main">
					<div v-if="$slots.title" class="title">
						<slot name="title"></slot>
					</div>
					<component :is="titleElement" v-else v-tooltip="title" class="title">
						{{ title }}
					</component>

					<div v-if="$slots.subtitle" class="subtitle type-note">
						<slot name="subtitle"></slot>
					</div>
					<p v-else-if="subtitle" class="subtitle type-note">
						{{ subtitle }}
					</p>
				</div>
				<v-contextual-menu
					v-if="options"
					class="context"
					:disabled="disabled"
					:options="options"
					@click="$emit($event)"
				></v-contextual-menu>
			</div>
		</component>
	</component>
</template>

<script>
export default {
	name: 'VCard',
	props: {
		element: {
			type: String,
			default: 'article'
		},
		titleElement: {
			type: String,
			default: 'h2'
		},
		icon: {
			type: String,
			default: null
		},
		color: {
			type: String,
			default: 'card-background-color'
		},
		textBackground: {
			type: Boolean,
			default: false
		},
		src: {
			type: String,
			default: null
		},
		title: {
			type: String,
			default: null
		},
		subtitle: {
			type: String,
			default: null
		},
		to: {
			type: String,
			default: null
		},
		href: {
			type: String,
			default: null
		},
		label: {
			type: String,
			default: null
		},
		opacity: {
			type: String,
			default: 'full',
			validator(val) {
				return ['full', 'half'].includes(val);
			}
		},
		selected: {
			type: Boolean,
			default: null
		},
		selectionMode: {
			type: Boolean,
			default: false
		},
		options: {
			type: Object,
			default: null
		},
		disabled: {
			type: Boolean,
			default: false
		},
		bigImage: {
			type: Boolean,
			default: false
		},
		mediumImage: {
			type: Boolean,
			default: false
		},
		onlyShowOnHover: {
			type: Boolean,
			default: false
		}
	},
	data() {
		return {
			error: null
		};
	},
	computed: {
		wrapperTag() {
			if (this.to) {
				return 'router-link';
			}

			if (this.href) {
				return 'a';
			}

			return 'div';
		},
		link() {
			if (this.to || this.href) {
				return true;
			}

			return false;
		},
		selectable() {
			return this.selected !== null;
		},
		selectionIcon() {
			if (this.selected) return 'check_circle';
			if (this.selectionMode && !this.selected) return 'radio_button_unchecked';
			return 'check_circle';
		}
	},
	watch: {
		src() {
			this.error = null;
		}
	},
	methods: {
		onImageError(error) {
			this.error = error;
		}
	}
};
</script>

<style lang="scss" scoped>
.v-card {
	width: var(--card-size);
	overflow: hidden;
	transition: box-shadow var(--fast) var(--transition);
	position: relative;

	&.text-background {
		.header {
			border-radius: var(--border-radius) var(--border-radius) 0 0;
			height: 244px;
		}
		.body {
			padding: 8px 12px;
			background-color: var(--input-background-color-alt);
			border-radius: 0 0 var(--border-radius) var(--border-radius);
			min-height: 56px;
		}
	}

	a {
		text-decoration: none;
		cursor: pointer;
		user-select: none;
	}

	&.link {
		&:not(.disabled):hover,
		&:not(.disabled).selected {
			.header:not(.big-image) {
				background-color: var(--card-background-color-hover) !important;
			}
		}
	}

	.header {
		transition: all var(--fast) var(--transition);
		height: var(--card-size);
		border-radius: var(--border-radius);
		background-color: var(--card-background-color);
		overflow: hidden;
		display: grid;
		grid-template-columns: 1;
		grid-template-rows: 1;
		align-items: center;
		justify-content: center;
		position: relative;

		&.big-image {
			height: 474px;
		}

		&.medium-image {
			height: 300px;
		}

		&.small {
			height: 40px;
		}

		.select {
			position: absolute;
			top: 0;
			left: 0;
			width: 40px;
			height: 40px;
			color: var(--white);
			opacity: 0;
			transition: opacity var(--fast) var(--transition);

			.v-icon {
				position: absolute;
				top: 10px;
				left: 10px;
				font-size: 20px;
			}

			&:hover,
			.user-is-tabbing &:focus,
			&.selected {
				transition: none;
				opacity: 1;
			}
		}

		img {
			width: 100%;
			height: 100%;
			object-fit: contain;
		}

		&.big-image img {
			width: 100%;
			height: 100%;
		}

		.icon {
			color: var(--card-text-color);
			text-align: center;
		}

		.custom-icon {
			width: 48px;
			height: 48px;
		}

		img,
		.icon {
			grid-row: 1;
			grid-column: 1;
		}

		.label {
			position: absolute;
			bottom: 10px;
			right: 10px;
			padding: 2px 5px;
			border-radius: var(--border-radius);
			opacity: 0.5;
			background-color: var(--white);
			color: var(--blue-grey-800);
			backdrop-filter: blur(5px);
			font-size: 10px;
			text-transform: uppercase;
		}
	}

	&.disabled {
		cursor: not-allowed;

		& .header {
			& .icon {
				color: var(--card-text-color-disabled) !important;
			}
			& .custom-icon {
				svg {
					fill: var(--card-text-color-disabled) !important;
				}
			}
		}
	}

	.body {
		padding-top: 8px;
		position: relative;
		display: flex;
		align-items: center;

		.main {
			position: relative;
			overflow: hidden;
			flex-grow: 1;
		}
	}

	.title,
	.subtitle {
		width: 100%;
		white-space: nowrap;
		text-overflow: ellipsis;
		overflow: hidden;
	}

	.title {
		//
	}

	.subtitle {
		color: var(--note-text-color);
	}

	.error {
		opacity: 0.2;
	}

	&.selectable {
		.select {
			transition: opacity var(--fast) var(--transition);
		}
		.header::before {
			content: '';
			position: absolute;
			width: 100%;
			height: 50px;
			max-height: 100%;
			left: 0;
			top: 0;
			opacity: 0;
			background-image: linear-gradient(-180deg, #263238 10%, rgba(38, 50, 56, 0) 100%);
			transition: opacity var(--fast) var(--transition);
		}

		&.selection-mode {
			.select {
				width: 100%;
				height: 100%;
			}
		}

		&:hover,
		&.selection-mode,
		&.selected {
			.select {
				transition: none;
				opacity: 0.7;

				&:hover {
					opacity: 1;
				}
			}

			.header::before {
				opacity: 0.3;
			}
		}

		&.selected .select {
			opacity: 1;
		}
	}

	.context {
		position: absolute;
		right: 2px;
		bottom: 15px;
	}
}

.only-show-on-hover {
	.body {
		position: absolute;
		bottom: 0;
		left: 0;
		width: 100%;
		opacity: 0;
		transition: opacity var(--fast) var(--transition);
	}

	&:hover .body {
		opacity: 1;
	}
}
</style>
