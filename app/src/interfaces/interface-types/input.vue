<template>
	<v-simple-select :value="value" @input="$emit('input', $event)">
		<option v-for="type in availableTypes" :key="type" :value="type">
			{{ $helpers.formatTitle(type) }}
		</option>
	</v-simple-select>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';
import { mapState } from 'vuex';

export default {
	name: 'InterfaceTypes',
	mixins: [mixin],
	watch: {
		interfaceName() {
			this.selectFirst();
		}
	},
	computed: {
		...mapState({
			interfaces: state => state.extensions.interfaces
		}),
		interfaceName() {
			const interfaceNameField = this.options.interfaceField;
			if (!interfaceNameField) return;
			return this.values[interfaceNameField];
		},
		availableTypes() {
			return this.interfaces[this.interfaceName]?.types;
		}
	},
	created() {
		this.selectFirst();
	},
	methods: {
		selectFirst() {
			if (this.availableTypes === undefined || this.availableTypes.length === 0) return;

			this.$emit('input', this.availableTypes[0]);
		}
	}
};
</script>
