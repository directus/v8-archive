<docs>
  # Notice Component

  ## Usage

  ```vue
  <v-notice color="warning">Some text here...</v-notice>
  ```

  ## Properties

  | name  | description                            | default |
  |-------|----------------------------------------|---------|
  | color | A color name out of the global pallete | gray    |
  | icon  | The name of the icon to render         | --      |
</docs>

<template>
	<p class="notice selectable" :class="color">
		<v-icon v-if="icon" class="icon" :name="icon" />
		<slot />
	</p>
</template>

<script>
export default {
	name: 'VNotice',
	props: {
		color: {
			type: String,
			default: 'gray',
			validator(val) {
				return ['gray', 'gray-subdued', 'accent', 'warning', 'danger', 'success'].includes(
					val
				);
			}
		},
		icon: {
			type: String,
			default: undefined
		}
	}
};
</script>

<style lang="scss" scoped>
.notice {
	position: relative;
	padding: 16px;
	border-radius: var(--border-radius);
	max-width: 632px;
	font-size: var(--input-font-size);
	vertical-align: middle;
	display: flex;
	align-items: center;
	.icon {
		margin-right: 12px;
		line-height: 0;
		margin-top: -2px;
	}
	&.gray {
		color: var(--notice-text-color);
		background-color: var(--notice-background-color);
	}
	&.gray-subdued {
		color: var(--notice-text-color-alt);
		background-color: var(--notice-background-color);
	}
	&.danger {
		color: var(--danger);
		background-color: var(--danger-light);
	}
	&.warning {
		color: var(--warning);
		background-color: var(--warning-light);
	}
	&.success {
		color: var(--success);
		background-color: var(--success-light);
	}
}
</style>
