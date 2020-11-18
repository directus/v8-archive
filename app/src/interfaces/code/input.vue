<template>
	<div class="interface-code codemirror-custom-styles">
		<codemirror
			ref="codemirrorEl"
			:options="altOptions ? altOptions : cmOptions"
			:value="stringValue"
			@input="onInput"
		></codemirror>

		<button
			v-if="options.template"
			v-tooltip="$t('interfaces.code.fill_template')"
			@click="fillTemplate"
		>
			<v-icon name="playlist_add" />
		</button>

		<small v-if="language" class="line-count type-note">
			{{
				$tc('interfaces.code.loc', lineCount, {
					count: lineCount,
					lang: language
				})
			}}
		</small>
	</div>
</template>

<script>
import { codemirror } from 'vue-codemirror';

import 'codemirror/lib/codemirror.css';

import 'codemirror/mode/vue/vue.js';
import 'codemirror/mode/javascript/javascript.js';
import 'codemirror/mode/php/php.js';

import 'codemirror/addon/selection/active-line.js';
import 'codemirror/addon/selection/mark-selection.js';
import 'codemirror/addon/search/searchcursor.js';
import 'codemirror/addon/hint/show-hint.js';
import 'codemirror/addon/hint/show-hint.css';
import 'codemirror/addon/hint/javascript-hint.js';
import 'codemirror/addon/selection/active-line.js';
import 'codemirror/addon/scroll/annotatescrollbar.js';
import 'codemirror/addon/search/matchesonscrollbar.js';
import 'codemirror/addon/search/searchcursor.js';
import 'codemirror/addon/search/match-highlighter.js';
import 'codemirror/addon/edit/matchbrackets.js';
import 'codemirror/addon/comment/comment.js';
import 'codemirror/addon/dialog/dialog.js';
import 'codemirror/addon/dialog/dialog.css';
import 'codemirror/addon/search/searchcursor.js';
import 'codemirror/addon/search/search.js';

import 'codemirror/addon/display/autorefresh.js';

import 'codemirror/keymap/sublime.js';

import mixin from '@directus/extension-toolkit/mixins/interface';

import { isObject, isArray } from 'lodash';

export default {
	name: 'InterfaceCode',
	components: {
		codemirror
	},
	mixins: [mixin],
	props: {
		altOptions: {
			type: Object
		}
	},
	data() {
		return {
			lineCount: 0,

			defaultOptions: {
				tabSize: 4,
				autoRefresh: true,
				indentUnit: 4,
				styleActiveLine: true,
				styleSelectedText: true,
				line: true,
				highlightSelectionMatches: { showToken: /\w/, annotateScrollbar: true },
				hintOptions: {
					completeSingle: true
				},
				matchBrackets: true,
				showCursorWhenSelecting: true,
				theme: 'default',
				extraKeys: { Ctrl: 'autocomplete' }
			}
		};
	},
	computed: {
		availableTypes() {
			return {
				'text/plain': 'Plain Text',
				'text/javascript': 'JavaScript',
				'application/json': 'JSON',
				'text/x-vue': 'Vue',
				'application/x-httpd-php': 'PHP'
			};
		},
		language() {
			return this.availableTypes[this.options.language];
		},
		stringValue() {
			if (this.value == null) return null;

			if (typeof this.value === 'object') {
				return JSON.stringify(this.value, null, 4);
			}

			return this.value;
		},
		mode() {
			// There is no dedicated mode for JSON in codemirror. Switch to JS mode when JSON is selected
			return this.options.language === 'application/json'
				? 'text/javascript'
				: this.options.language;
		},
		cmOptions() {
			return Object.assign({}, this.defaultOptions, {
				lineNumbers: this.options.lineNumber,
				readOnly: this.readonly ? 'nocursor' : false,
				mode: this.mode
			});
		}
	},
	mounted() {
		const { codemirror } = this.$refs.codemirrorEl;
		this.lineCount = codemirror.lineCount();
	},
	methods: {
		onInput(value) {
			const { codemirror } = this.$refs.codemirrorEl;

			if (this.lineCount !== codemirror.lineCount()) {
				this.lineCount = codemirror.lineCount();
			}

			if (this.options.language === 'application/json') {
				try {
					this.$emit('input', JSON.parse(value));
				} catch (e) {
					// silently ignore saving value if it's not valid json
				}
			} else {
				this.$emit('input', value);
			}
		},
		fillTemplate() {
			if (isObject(this.options.template) || isArray(this.options.template)) {
				return this.$emit('input', JSON.stringify(this.options.template, null, 4));
			}

			if (this.options.language === 'application/json') {
				try {
					this.$emit('input', JSON.parse(this.options.template));
				} catch (e) {
					// silently ignore saving value if it's not valid json
				}
			} else {
				this.$emit('input', this.options.template);
			}
		}
	}
};
</script>

<style lang="scss" scoped>
.interface-code {
	position: relative;
	width: 100%;
	max-width: var(--width-x-large);
	// border: var(--input-border-width) solid var(--input-border-color);
	// border-radius: var(--border-radius);
	font-size: var(--input-font-size);

	&:focus {
		border-color: var(--blue-grey-800);
	}
}

small {
	position: absolute;
	right: 0;
	bottom: -20px;
	font-style: italic;
	text-align: right;
}

button {
	position: absolute;
	top: 10px;
	right: 10px;
	user-select: none;
	color: var(--blue-grey-300);
	cursor: pointer;
	transition: color var(--fast) var(--transition-out);
	z-index: 10;

	&:hover {
		transition: none;
		color: var(--blue-grey-600);
	}
}
</style>
