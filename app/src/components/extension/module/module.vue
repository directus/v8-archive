<template>
	<component :is="componentName"><slot /></component>
</template>

<script>
import Vue from 'vue';
import loadExtension from '../../../helpers/load-extension';
import componentExists from '../../../helpers/component-exists';
import moduleFallback from './module-fallback.vue';
import moduleLoading from './module-loading.vue';

export default {
	name: 'VExtmodule',
	props: {
		id: {
			type: String,
			required: true
		}
	},
	computed: {
		modules() {
			return this.$store.state.extensions.modules;
		},
		module() {
			return this.modules && this.modules[this.id];
		},
		componentName() {
			return `module-${this.id}`;
		}
	},
	watch: {
		id() {
			this.registerModule();
		}
	},
	created() {
		this.registerModule();
	},
	methods: {
		/**
		 * Register the extension as component (if it hasn't been registered before yet)
		 */
		registerModule() {
			// If component already exists, do nothing
			if (componentExists(this.componentName)) return;

			// If the extension isn't known by the API (e.g. it's not in the store), register it with the
			//   fallback immediately
			if (!this.module) {
				Vue.component(this.componentName, moduleFallback);
				return;
			}

			let component;

			if (this.module.core) {
				component = import('@/interfaces/' + this.module.id + 'input.vue');
			} else {
				const filePath = `${this.$store.state.apiRootPath}${this.module.path.replace(
					'meta.json',
					'module.js'
				)}`;

				component = loadExtension(filePath);
			}

			Vue.component(this.componentName, () => ({
				component: component,
				error: moduleFallback,
				loading: moduleLoading
			}));
		}
	}
};
</script>
