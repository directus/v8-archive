<template>
	<div class="interface-repeater">
		<draggable
			v-model="rows"
			handle=".drag-handle"
			class="row-container"
			:class="{ dragging }"
			@start="dragging = true"
			@end="endDrag"
		>
			<repeater-row
				v-for="(row, index) in rows"
				:key="row.__key"
				:row="row"
				:fields="repeaterFields"
				:inline="inline"
				:template="options.template"
				:duplicable="options.duplicable"
				:open="open === index"
				:placeholder="options.placeholder"
				@open="toggleOpen(index)"
				@input="updateRow(index, $event)"
				@remove="removeRow(index)"
				@duplicate="duplicateRow(index)"
			/>
		</draggable>
		<div v-if="addButtonVisible" class="add-new" @click="addRow">
			<v-icon name="add" color="--input-icon-color" />
			{{ options.createItemText }}
		</div>
	</div>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';
import RepeaterRow from './row';
import shortid from 'shortid';
import { clone, keyBy } from 'lodash';

export default {
	name: 'Repeater',
	components: {
		RepeaterRow
	},
	mixins: [mixin],
	data() {
		return {
			rows: [],
			dragging: false,
			open: null
		};
	},
	computed: {
		// If the interface is able to display the fields inline
		inline() {
			// Only render inline when there are 1 or two fields
			if (this.repeaterFields.length > 2) {
				return false;
			}

			// If there's only 1 field, render it regardless of size
			if (this.repeaterFields.length === 1) {
				return true;
			}

			// Only render inline if there's enough space in the parent
			// We want to prevent the two interfaces being squashed togt
			if (['full', 'fill'].includes(this.width) === false) {
				return false;
			}

			// Only render inline if every field is configured to be half width
			return this.repeaterFields.every(field => field.width === 'half');
		},
		addButtonVisible() {
			if (!this.options.limit || this.options.limit === 0) return true;
			if (this.rows.length < this.options.limit) return true;

			return false;
		},

		// This makes sure that the fields are always an array of fields. It makes sure the interface
		// is backwards compatible for people who have their repeater setup in an object structure
		repeaterFields() {
			if (Array.isArray(this.options.fields)) {
				return this.options.fields;
			}

			return Object.keys(this.options.fields).map(key => {
				return {
					...this.options.fields[key],
					field: key
				};
			});
		}
	},
	created() {
		this.setRows();
	},
	methods: {
		addRow() {
			this.rows = [...this.rows, this.getNewRow()];
			this.open = this.rows.length - 1;
			this.emitValue();
		},
		updateRow(index, { field, value }) {
			const rows = clone(this.rows);
			const currentRow = rows[index];
			const newRow = {
				...currentRow,
				[field]: value
			};

			rows[index] = newRow;

			this.rows = rows;
			this.emitValue();
		},
		removeRow(index) {
			const newRows = clone(this.rows);
			newRows.splice(index, 1);
			this.rows = newRows;
			this.emitValue();
		},
		duplicateRow(index) {
			const newRows = clone(this.rows);
			const duplicatedRow = clone(this.rows[index]);

			newRows.splice(index + 1, 0, duplicatedRow);
			this.rows = newRows;
			this.emitValue();
		},

		emitValue() {
			let value = clone(this.rows).map(row => {
				delete row.__key;
				return row;
			});

			if (value.length === 0) {
				return this.$emit('input', null);
			}

			if (this.options.structure === 'object') {
				this.$emit('input', keyBy(value, this.options.structure_key));
			} else {
				this.$emit('input', value);
			}
		},
		getNewRow() {
			const row = {
				__key: shortid.generate(),
				newItem: true
			};

			this.repeaterFields.forEach(field => {
				row[field.field] = field.default;
			});

			return row;
		},
		// There's two different structures in which the interface value can be saved
		// This method makes sure we're always using the same structure in the interface itself
		setRows() {
			if (this.value === null) {
				this.rows = [];
				return;
			}

			if (Array.isArray(this.value)) {
				this.rows = this.value;
			} else if (typeof this.value === 'string') {
				try {
					this.rows = JSON.parse(this.value);
				} catch {
					console.warn('Invalid JSON passed to repeater');
				}
			} else {
				this.rows = Object.values(this.value);
			}
		},
		endDrag() {
			this.dragging = false;
			this.emitValue();
		},
		toggleOpen(index) {
			if (this.open === index) {
				this.open = null;
			} else {
				this.open = index;
			}
		}
	}
};
</script>

<style lang="scss" scoped>
.notice {
	margin-bottom: 12px;
}

.add-new {
	display: flex;
	align-items: center;
	padding: var(--input-padding);
	border: var(--input-border-width) dotted var(--input-border-color);
	border-radius: var(--border-radius);
	color: var(--input-text-color);
	transition: var(--fast) var(--transition);
	transition-property: color, border-color, padding;
	min-height: var(--input-height);
	font-size: var(--input-font-size);
	padding: var(--input-padding);
	cursor: pointer;
	&:hover {
		border-color: var(--input-border-color-hover);
	}
	i {
		margin-right: 8px;
	}
}
</style>
