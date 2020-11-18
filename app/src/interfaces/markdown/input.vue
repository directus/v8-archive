<template>
	<div class="interface-markdown" :class="{ float: !options.tabbed, readonly }">
		<v-textarea
			v-if="!preview || !options.tabbed"
			:id="name"
			class="textarea"
			:value="value"
			:placeholder="options.placeholder"
			:rows="+options.rows"
			:disabled="readonly"
			:readonly="readonly"
			@input="$emit('input', $event)"
		></v-textarea>
		<div
			v-if="preview || !options.tabbed"
			class="preview"
			:style="{ height: options.rows * 23 + 'px' }"
			v-html="compiledMarkdown"
		></div>
		<div v-if="options.tabbed" class="toolbar">
			<span class="tab" :class="{ active: !preview }" @click="preview = false">
				<v-icon name="code" />
				{{ $t('interfaces.markdown.edit') }}
			</span>
			<span class="tab" :class="{ active: preview }" @click="preview = true">
				<v-icon name="visibility" />
				{{ $t('interfaces.markdown.preview') }}
			</span>
		</div>
	</div>
</template>

<script>
import marked from 'marked';
import mixin from '@directus/extension-toolkit/mixins/interface';

export default {
	mixins: [mixin],
	data() {
		return {
			preview: false
		};
	},
	computed: {
		compiledMarkdown() {
			if (this.value) {
				return marked(this.value, {
					sanitize: true
				});
			}
			return this.value;
		}
	}
};
</script>

<style lang="scss" scoped>
.interface-markdown {
	position: relative;
	display: flex;
	flex-wrap: wrap;
	flex-direction: column-reverse;
	width: 100%;
	color: var(--input-text-color);
	&.float {
		flex-wrap: nowrap;
		flex-direction: row;
		.textarea {
			margin-right: 20px;
			width: calc((100% - 20px) / 2);
			border-radius: var(--border-radius);
		}
		.preview {
			width: calc((100% - 20px) / 2);
			border-radius: var(--border-radius);
		}
	}
	.toolbar {
		display: flex;
		border-radius: var(--border-radius) var(--border-radius) 0 0;
		border: var(--input-border-width) solid var(--input-border-color);
		border-bottom: none;
		background-color: var(--input-background-color-alt);
		width: 100%;
		font-size: var(--input-font-size);

		.tab {
			transition: color var(--fast) var(--transition);
			cursor: pointer;
			padding: 10px 16px 10px 12px;
			line-height: 20px;

			&.active {
				font-weight: 500;
				border-radius: var(--border-radius) var(--border-radius) 0 0;
				border: var(--input-border-width) solid var(--input-border-color);
				border-bottom: none;
				background-color: var(--input-background-color);
				margin: -2px;
				z-index: 1;
			}

			.v-icon {
				margin-right: 4px;
				margin-top: -2px;
			}
		}
	}
	.textarea,
	.preview {
		flex-grow: 1;
		width: 100%;
		max-width: var(--width-large);
		min-height: 200px;
		max-height: 800px;
		overflow: auto;
		border-radius: 0 0 var(--border-radius) var(--border-radius);
	}
	.textarea {
		font-family: 'Roboto Mono', monospace;
	}
	.preview {
		background-color: var(--input-background-color);
		padding: 10px;
		border: var(--input-border-width) solid var(--input-border-color);
	}

	&:not(.readonly).textarea:hover ~ .toolbar span.active {
		border-color: var(--input-border-color-hover);
	}
	&:not(.readonly).textarea:focus ~ .toolbar span.active {
		border-color: var(--input-border-color-focus);
	}
}
</style>

<style lang="scss">
.interface-markdown {
	.textarea {
		resize: vertical;
	}
	.preview {
		font-size: 14px;
		line-height: 1.6;
		font-weight: 400;

		& > *:first-child {
			margin-top: 0;
		}
		& > *:last-child {
			margin-bottom: 0;
		}

		h1,
		h2,
		h3,
		h4,
		h5,
		h6 {
			margin: 20px 0 10px;
			padding: 0;
			font-weight: 600;
			cursor: text;
			position: relative;
		}

		h1 tt,
		h1 code {
			font-size: inherit;
		}
		h2 tt,
		h2 code {
			font-size: inherit;
		}
		h3 tt,
		h3 code {
			font-size: inherit;
		}
		h4 tt,
		h4 code {
			font-size: inherit;
		}
		h5 tt,
		h5 code {
			font-size: inherit;
		}
		h6 tt,
		h6 code {
			font-size: inherit;
		}

		h1 {
			font-size: 28px;
		}
		h2 {
			font-size: 24px;
		}
		h3 {
			font-size: 18px;
		}
		h4 {
			font-size: 16px;
		}
		h5 {
			font-size: 14px;
		}
		h6 {
			font-size: 14px;
			color: var(--blue-grey-400);
		}

		p,
		blockquote,
		ul,
		ol,
		dl,
		li,
		table,
		pre {
			margin: 15px 0;
		}

		& > h2:first-child {
			margin-top: 0;
			padding-top: 0;
		}
		& > h1:first-child {
			margin-top: 0;
			padding-top: 0;
		}
		& > h1:first-child + h2 {
			margin-top: 0;
			padding-top: 0;
		}
		& > h3:first-child,
		& > h4:first-child,
		& > h5:first-child,
		& > h6:first-child {
			margin-top: 0;
			padding-top: 0;
		}

		a:first-child h1,
		a:first-child h2,
		a:first-child h3,
		a:first-child h4,
		a:first-child h5,
		a:first-child h6 {
			margin-top: 0;
			padding-top: 0;
		}

		h1 p,
		h2 p,
		h3 p,
		h4 p,
		h5 p,
		h6 p {
			margin-top: 0;
		}

		li p.first {
			display: inline-block;
		}
		ul,
		ol {
			padding-left: 30px;
			li {
				margin: 0;
			}
		}
		ul :first-child,
		ol :first-child {
			margin-top: 0;
		}
		ul :last-child,
		ol :last-child {
			margin-bottom: 0;
		}

		blockquote {
			border-left: 4px solid var(--blue-grey-50);
			padding: 0 15px;
			color: var(--blue-grey-400);
		}
		blockquote > :first-child {
			margin-top: 0;
		}
		blockquote > :last-child {
			margin-bottom: 0;
		}

		table {
			padding: 0;
			border-spacing: 0;
			border-collapse: collapse;
		}
		table tr {
			border-top: 1px solid var(--blue-grey-50);
			background-color: white;
			margin: 0;
			padding: 0;
		}
		table tr:nth-child(2n) {
			background-color: var(--page-background-color);
		}
		table tr th {
			font-weight: bold;
			border: 1px solid var(--blue-grey-50);
			text-align: left;
			margin: 0;
			padding: 6px 13px;
		}
		table tr td {
			border: 1px solid var(--blue-grey-50);
			text-align: left;
			margin: 0;
			padding: 6px 13px;
		}
		table tr th :first-child,
		table tr td :first-child {
			margin-top: 0;
		}
		table tr th :last-child,
		table tr td :last-child {
			margin-bottom: 0;
		}

		img {
			max-width: 100%;
		}

		code,
		tt {
			font-family: 'Roboto Mono', mono;
			margin: 0 2px;
			padding: 0 5px;
			white-space: nowrap;
			border: 1px solid var(--blue-grey-50);
			background-color: var(--page-background-color);
			border-radius: var(--border-radius);
		}
		pre code {
			margin: 0;
			padding: 0;
			white-space: pre;
			border: none;
			background: transparent;
		}
		.highlight pre {
			background-color: var(--page-background-color);
			border: 1px solid var(--blue-grey-50);
			font-size: 13px;
			line-height: 19px;
			overflow: auto;
			padding: 6px 10px;
			border-radius: var(--border-radius);
		}
		pre {
			font-family: 'Roboto Mono', mono;
			background-color: var(--page-background-color);
			border: 1px solid var(--blue-grey-50);
			font-size: 13px;
			line-height: 19px;
			overflow: auto;
			padding: 6px 10px;
			border-radius: var(--border-radius);
		}
		pre code,
		pre tt {
			background-color: transparent;
			border: none;
		}

		hr {
			border: none;
			border-top: 1px solid var(--blue-grey-50);
			margin: 20px auto;
		}

		b,
		strong {
			font-weight: 600;
		}

		a {
			//
		}
	}
}
</style>
