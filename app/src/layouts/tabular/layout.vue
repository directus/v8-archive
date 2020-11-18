<template>
	<div class="layout">
		<v-table
			ref="table"
			:class="viewOptions.spacing"
			:items="items"
			:columns="columns"
			:collection="collection"
			:primary-key-field="primaryKeyField"
			:selection="selection"
			:sort-val="sortVal"
			:row-height="rowHeight"
			:loading="loading"
			:lazy-loading="lazyLoading"
			:column-widths="viewOptions.widths || {}"
			:link="link"
			:use-interfaces="true"
			:manual-sort-field="sortField"
			@sort="sort"
			@widths="setWidths"
			@select="$emit('select', $event)"
			@scroll-end="$emit('next-page')"
			@input="$emit('input', $event)"
		></v-table>
	</div>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/layout';

export default {
	mixins: [mixin],
	computed: {
		columns() {
			const fieldValues = Object.values(this.fields);

			let queryFields;

			if (this.viewQuery.fields) {
				if (Array.isArray(this.viewQuery.fields)) {
					queryFields = this.viewQuery.fields;
				} else {
					queryFields = this.viewQuery.fields.split(',');
				}
			} else {
				queryFields = fieldValues
					.filter(field => field.primary_key === false || field.primary_key === '0')
					.filter(field => field.hidden_browse !== true)
					.slice(0, 4)
					.map(field => field.field);
			}

			return queryFields
				.filter(field => this.fields[field])
				.map(fieldID => {
					const fieldInfo = this.fields[fieldID];
					const name = fieldInfo.name;
					return { field: fieldID, name, fieldInfo };
				});
		},
		rowHeight() {
			if (this.viewOptions.spacing === 'comfortable') {
				return 48;
			}

			if (this.viewOptions.spacing === 'cozy') {
				return 40;
			}

			if (this.viewOptions.spacing === 'compact') {
				return 32;
			}

			return 48;
		},
		sortVal() {
			let sortQuery = (this.viewQuery && this.viewQuery['sort']) || this.primaryKeyField;

			return {
				asc: !sortQuery.startsWith('-'),
				field: sortQuery.replace('-', '')
			};
		}
	},
	watch: {
		sortVal(newVal, oldVal) {
			if (newVal !== oldVal) {
				this.$refs.table.$el.scrollTop = 0;
			}
		}
	},
	methods: {
		sort(sortVal) {
			const sortValString = (sortVal.asc ? '' : '-') + sortVal.field;

			this.$emit('query', {
				sort: sortValString
			});
		},
		setWidths(widths) {
			this.$emit('options', {
				widths
			});
		}
	}
};
</script>

<style lang="scss" scoped>
.layout {
	margin-top: var(--page-padding-top-table);
	padding: 0 var(--page-padding);
}
</style>
