<template>
	<v-notice v-if="Object.keys(interfaceOptions).length === 0">
		{{ $t('interface_has_no_options') }}
	</v-notice>
	<v-sheet v-else-if="interfaceOptions">
		<form @submit.prevent>
			<div v-for="(option, optionID) in interfaceOptions" :key="optionID" class="options">
				<label :for="optionID">{{ option.name }}</label>
				<v-ext-input
					:id="option.interface"
					:name="optionID"
					:type="option.type"
					:length="option.length"
					:readonly="option.readonly"
					:required="option.required"
					:loading="option.loading"
					:options="option.options"
					:value="(value || {})[optionID]"
					:fields="interfaceOptions"
					:values="val"
					@input="stageValue(optionID, $event)"
				/>
				<p class="note" v-html="$helpers.snarkdown(option.comment || '')" />
			</div>
		</form>
	</v-sheet>
	<v-notice v-else>
		{{ $t('select_interface') }}
	</v-notice>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';
import { clone, merge } from 'lodash';

export default {
	name: 'InterfaceOptions',
	mixins: [mixin],
	computed: {
		interfaceName() {
			const interfaceNameField = this.options.interfaceField;
			if (!interfaceNameField) return;
			return this.values[interfaceNameField];
		},
		interfaceOptions() {
			if (!this.interfaceName) return;
			return this.$store.state.extensions.interfaces[this.interfaceName].options;
		},
		val() {
			return Array.isArray(this.value) ? {} : this.value;
		}
	},
	methods: {
		stageValue(field, value) {
			const oldValue = clone(this.value || {});
			const newValue = merge(oldValue, { [field]: value });
			this.$emit('input', newValue);
		}
	}
};
</script>

<style lang="scss" scoped>
label {
	margin-bottom: 8px;
	font-size: 15px;
	margin-bottom: 8px;
}

div.options {
	margin-bottom: 30px;

	&:last-of-type {
		margin-bottom: 0;
	}
}

.note {
	display: block;
	margin-top: 4px;
	margin-bottom: 10px;
	font-style: italic;
	font-size: var(--size-3);
	line-height: 1.5em;
	color: var(--blue-grey-300);
	font-weight: var(--weight-bold);
}
</style>
