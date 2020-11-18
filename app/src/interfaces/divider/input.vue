<template>
	<div :class="[`${options.style}-style`, { margin: options.margin }]">
		<h2 v-if="options.title">{{ options.title }}</h2>
		<hr v-if="options.hr" />
		<p v-if="options.description">{{ options.description }}</p>
	</div>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';

export default {
	mixins: [mixin]
};
</script>

<style lang="scss" scoped>
.small-style {
	position: relative;
	padding-top: 12px;
	&.margin {
		margin-top: 48px;
	}
	h2 {
		position: absolute;
		top: 0px;
		left: 50%;
		transform: translateX(-50%);

		color: var(--heading-text-color);
		font-size: 16px;
		line-height: 18px;
		background-color: var(--page-background-color);
		padding: 0 12px;
	}
	p {
		position: absolute;
		top: 28px;
		left: 50%;
		transform: translateX(-50%);
		color: var(--note-text-color);
		font-size: var(--input-font-size);
		max-width: 560px;
	}
	hr {
		border: 0;
		background: none;
		border-bottom: 2px solid var(--input-border-color);
	}
}

.medium-style {
	padding-top: 12px;
	&.margin {
		margin-top: 48px;
	}
	h2 {
		color: var(--heading-text-color);
		font-size: 22px;
		line-height: 28px;
		font-weight: 400;
		margin-bottom: 12px;
	}
	p {
		color: var(--note-text-color);
		max-width: 560px;
		margin-top: 12px;
		font-size: var(--input-font-size);
	}
	hr {
		border: 0;
		background: none;
		border-bottom: 2px solid var(--input-border-color);
	}
}

.large-style {
	padding-top: 12px;
	&.margin {
		margin-top: 48px;
	}
	h2 {
		color: var(--heading-text-color);
		font-size: 28px;
		line-height: 32px;
		font-weight: 300;
		margin-bottom: 12px;
	}
	p {
		color: var(--note-text-color);
		font-size: var(--input-font-size);
		max-width: 560px;
		margin-top: 16px;
	}
	hr {
		border: 0;
		background: none;
		border-bottom: 2px solid var(--input-border-color);
	}
}
</style>
