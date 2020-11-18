<template>
	<component
		:is="componentName"
		:primary-key-field="primaryKeyField"
		:fields="fields"
		:view-options="viewOptions"
		:loading="loading"
		:view-query="viewQuery"
		:selection="selection"
		:layout-name="type"
		class="v-ext-layout-options"
		@query="$emit('query', $event)"
		@select="$emit('select', $event)"
		@options="$emit('options', $event)"
	/>
</template>

<script>
import Vue from 'vue';
import loadExtension from '../../../../helpers/load-extension';
import componentExists from '../../../../helpers/component-exists';
import VExtLayoutOptionsFallback from './layout-options-fallback.vue';
import VExtLayoutOptionsLoading from './layout-options-loading.vue';

export default {
	name: 'VExtLayoutOptions',
	props: {
		type: {
			type: String,
			required: true
		},
		fields: {
			type: Object,
			required: true
		},
		viewOptions: {
			type: Object,
			default: () => ({})
		},
		loading: {
			type: Boolean,
			default: false
		},
		viewQuery: {
			type: Object,
			default: () => ({})
		},
		selection: {
			type: Array,
			default: () => []
		},
		primaryKeyField: {
			type: String,
			required: true
		}
	},
	computed: {
		layouts() {
			return this.$store.state.extensions.layouts;
		},
		layout() {
			return this.layouts && this.layouts[this.type];
		},
		componentName() {
			return `layout-options-${this.type}`;
		}
	},
	watch: {
		type() {
			this.registerLayoutOptions();
		}
	},
	created() {
		this.registerLayoutOptions();
	},
	methods: {
		/**
		 * Register the extension as component (if it hasn't been registered before yet)
		 */
		registerLayoutOptions() {
			// If component already exists, do nothing
			if (componentExists(this.componentName)) return;

			// If the extension isn't known by the API (e.g. it's not in the store), register it with the
			//   fallback immediately
			if (!this.layout) {
				Vue.component(this.componentName, VExtLayoutOptionsFallback);
				return;
			}

			let component;

			if (this.layout.core) {
				component = import('@/layouts/' + this.layout.id + '/options.vue');
			} else {
				const filePath = `${this.$store.state.apiRootPath}${this.layout.path.replace(
					'meta.json',
					'options.js'
				)}`;

				component = loadExtension(filePath);
			}

			Vue.component(this.componentName, () => ({
				component: component,
				error: VExtLayoutOptionsFallback,
				loading: VExtLayoutOptionsLoading
			}));
		}
	}
};
</script>
