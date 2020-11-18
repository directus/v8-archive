<template>
	<component
		:is="componentName"
		:id="name"
		:name="name"
		:value="value"
		:type="type"
		:length="length"
		:values="values"
		:collection="collection"
		:relation="relation"
		:readonly="readonly"
		:required="required"
		:loading="loading"
		:options="optionsWithDefaults"
		class="v-ext-display"
	/>
</template>

<script>
import Vue from 'vue';
import loadExtension from '../../../../helpers/load-extension';
import componentExists from '../../../../helpers/component-exists';
import VExtDisplayFallback from './display-fallback.vue';
import VExtDisplayLoading from './display-loading.vue';
import { datatypes } from '../../../../type-map';
import { mapValues } from 'lodash';

export default {
	name: 'VExtDisplay',
	components: {
		VExtDisplayFallback
	},
	props: {
		interfaceType: {
			type: String,
			default: 'text-input'
		},
		name: {
			type: String,
			required: true
		},
		value: {
			type: null,
			default: null
		},
		type: {
			type: String,
			default: null
		},
		datatype: {
			type: String,
			default: null
		},
		length: {
			type: [String, Number],
			default: null
		},
		values: {
			type: Object,
			default: null
		},
		collection: {
			type: String,
			default: null
		},
		readonly: {
			type: Boolean,
			default: false
		},
		relation: {
			type: Object,
			default: null
		},
		required: {
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
		}
	},
	computed: {
		interfaces() {
			return this.$store.state.extensions.interfaces;
		},
		interfaceInfo() {
			if (this.interfaceType === null) return this.interfaceFallback;
			return this.interfaces && this.interfaces[this.interfaceType];
		},
		componentName() {
			if (this.interfaceType === null) return this.componentNameFallback;
			return `display-${this.interfaceType}`;
		},
		componentNameFallback() {
			return `display-${this.interfaceFallback.id}`;
		},
		databaseVendor() {
			return this.$store.state.serverInfo.databaseVendor;
		},
		interfaceFallback() {
			// Default to text-input if all else fails
			if (this.datatype == null) return this.interfaces['text-input'];

			// Lookup the raw db datatype based on the current vendor in the type-map
			// to extract the fallback interface to use.
			const fallback = datatypes[this.databaseVendor][this.datatype].fallbackInterface;

			return this.interfaces[fallback];
		},
		optionsWithDefaults() {
			if (!this.interfaceInfo) return {};

			const defaults = mapValues(
				this.interfaceInfo.options,
				settings => settings.default || null
			);

			return {
				...defaults,
				...this.options
			};
		}
	},
	watch: {
		id() {
			this.registerDisplay();
		}
	},
	created() {
		this.registerDisplay();
	},
	methods: {
		/**
		 * Register the extension as component (if it hasn't been registered before yet)
		 */
		registerDisplay() {
			// If component already exists, do nothing
			if (componentExists(this.componentName)) return;

			let component;

			if (!this.interfaceInfo) {
				component = VExtDisplayFallback;
			} else if (this.interfaceInfo.core) {
				component = import('@/interfaces/' + this.interfaceInfo.id + '/display.vue');
			} else {
				const filePath = `${this.$store.state.apiRootPath}${this.interfaceInfo.path.replace(
					'meta.json',
					'display.js'
				)}`;

				component = loadExtension(filePath);
			}

			Vue.component(this.componentName, () => ({
				component: component,
				error: VExtDisplayFallback,
				loading: VExtDisplayLoading
			}));
		}
	}
};
</script>
