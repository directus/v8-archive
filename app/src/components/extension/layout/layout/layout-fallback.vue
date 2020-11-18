<template>
	<v-table
		v-if="items && columns && primaryKeyField"
		:items="items"
		:columns="columns"
		:primary-key-field="primaryKeyField"
		:use-interfaces="true"
		link="__link__"
		class="v-ext-layout-fallback"
	/>
</template>

<script>
export default {
	name: 'VExtLayoutFallback',
	props: {
		fields: {
			type: Object,
			required: true
		},
		items: {
			type: Array,
			required: true
		},
		primaryKeyField: {
			type: String,
			required: true
		},
		layoutName: {
			type: String,
			default: ''
		}
	},
	computed: {
		columns() {
			return Object.values(this.fields).map(field => {
				return {
					field: field.field,
					name: field.name,
					fieldInfo: field
				};
			});
		}
	},
	created() {
		this.$notify({
			title: this.$t('extension_error', { ext: this.layoutName }),
			color: 'red',
			iconMain: 'error'
		});
	}
};
</script>
