<template>
	<v-ext-input
		id="repeater"
		type="json"
		:name="name"
		:input-name="id"
		:value="formattedValue"
		:length="length"
		:readonly="readonly"
		:required="required"
		:options="repeaterOptions"
		:new-item="newItem"
		:relation="relation"
		:fields="fields"
		:collection="collection"
		:values="values"
		width="full"
		@input="emitValue"
	/>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';

export default {
	mixins: [mixin],
	computed: {
		repeaterOptions() {
			return {
				fields: [
					{
						field: 'key',
						interface: this.options.keyInterface,
						options: this.options.keyOptions,
						type: this.options.keyType,
						width: 'half'
					},
					{
						field: 'value',
						interface: this.options.valueInterface,
						options: this.options.valueOptions,
						type: this.options.valueType,
						width: 'half'
					}
				]
			};
		},
		formattedValue() {
			if (this.value === null) return null;

			return Object.keys(this.value).map(key => ({
				key,
				value: this.value[key]
			}));
		}
	},
	methods: {
		emitValue(value) {
			if (value === null) {
				this.$emit('input', null);
			} else {
				const formattedValue = {};
				value.forEach(({ key, value }) => (formattedValue[key] = value));
				this.$emit('input', formattedValue);
			}
		}
	}
};
</script>
