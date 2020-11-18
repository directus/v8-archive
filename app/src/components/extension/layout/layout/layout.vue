<template>
	<component
		:is="componentName"
		:layout-name="viewType"
		:primary-key-field="primaryKeyField"
		:fields="fields"
		:items="items"
		:collection="collection"
		:view-options="viewOptions"
		:view-query="viewQuery"
		:loading="loading"
		:lazy-loading="lazyLoading"
		:selection="selection"
		:link="link"
		:sort-field="sortField"
		class="v-layout"
		@query="$emit('query', $event)"
		@select="$emit('select', $event)"
		@options="$emit('options', $event)"
		@next-page="$emit('next-page', $event)"
		@input="$emit('input', $event)"
	/>
</template>

<script>
import Vue from 'vue';
import loadExtension from '../../../../helpers/load-extension';
import componentExists from '../../../../helpers/component-exists';
import VExtLayoutFallback from './layout-fallback.vue';
import VExtLayoutLoading from './layout-loading.vue';
import { filter } from 'lodash';

export default {
	name: 'VLayout',
	props: {
		fields: {
			type: Object,
			required: true
		},
		items: {
			type: Array,
			required: true
		},
		viewType: {
			type: String,
			required: true
		},
		viewOptions: {
			type: Object,
			default: () => ({})
		},
		viewQuery: {
			type: Object,
			default: () => ({})
		},
		selection: {
			type: Array,
			default: () => []
		},
		loading: {
			type: Boolean,
			default: false
		},
		lazyLoading: {
			type: Boolean,
			default: false
		},
		link: {
			type: String,
			default: null
		},
		collection: {
			type: String,
			default: null
		},
		sortField: {
			type: String,
			default: null
		}
	},
	computed: {
		layouts() {
			return this.$store.state.extensions.layouts;
		},
		layout() {
			return this.layouts && this.layouts[this.viewType];
		},
		componentName() {
			return `layout-${this.viewType}`;
		},
		primaryKeyField() {
			const fieldInfo = filter(this.fields, info => info.primary_key === true)[0];

			return fieldInfo && fieldInfo.field;
		}
	},
	watch: {
		viewType() {
			this.registerLayout();
		}
	},
	created() {
		this.registerLayout();
	},
	methods: {
		/**
		 * Register the extension as component (if it hasn't been registered before yet)
		 */
		registerLayout() {
			// If component already exists, do nothing
			if (componentExists(this.componentName)) return;

			// If the extension isn't known by the API (e.g. it's not in the store), register it with the
			//   fallback immediately
			if (!this.layout) {
				Vue.component(this.componentName, VExtLayoutFallback);
				return;
			}

			let component;

			if (this.layout.core) {
				component = import('@/layouts/' + this.layout.id + '/layout.vue');
			} else {
				const filePath = `${this.$store.state.apiRootPath}${this.layout.path.replace(
					'meta.json',
					'layout.js'
				)}`;

				component = loadExtension(filePath);
			}

			Vue.component(this.componentName, () => ({
				component: component,
				error: VExtLayoutFallback,
				loading: VExtLayoutLoading
			}));
		}
	}
};
</script>
