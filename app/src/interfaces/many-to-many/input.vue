<template>
	<div class="interface-many-to-many">
		<v-notice v-if="relationshipSetup === false" color="warning" icon="warning">
			{{ $t('relationship_not_setup') }}
		</v-notice>

		<v-spinner v-else-if="initialValue === null" />

		<template v-else>
			<div v-if="items && items.length" class="table">
				<div class="header">
					<div class="row">
						<button v-if="sortable" class="sort-column" @click="toggleManualSort">
							<v-icon
								name="sort"
								small
								:color="manualSortActive ? '--action' : '--blue-grey-300'"
							/>
						</button>
						<button
							v-for="field in visibleFields"
							:key="field.field"
							type="button"
							class="type-table-head"
							@click="changeSort(field.field)"
						>
							{{ $helpers.formatField(field.field, field.collection) }}
							<v-icon
								v-if="sort.field === field.field"
								:name="sort.asc ? 'arrow_downward' : 'arrow_upward'"
								:size="16"
							/>
						</button>
					</div>
				</div>
				<draggable
					v-model="itemsSorted"
					class="body"
					handle=".drag-handle"
					:disabled="!sortable || !manualSortActive"
					:class="{ dragging, readonly }"
					@start="dragging = true"
					@end="dragging = false"
				>
					<div
						v-for="item in itemsSorted"
						:key="item[junctionRelatedKey][relatedPrimaryKeyField]"
						class="row"
						@click="startEdit(item[junctionPrimaryKey])"
					>
						<div
							v-if="sortable"
							class="sort-column"
							:class="{ disabled: !manualSortActive }"
						>
							<v-icon v-if="!readonly" name="drag_handle" class="drag-handle" />
						</div>
						<div v-for="field in visibleFields" :key="field.field">
							<v-ext-display
								:interface-type="field.interface"
								:name="field.field"
								:type="field.type"
								:collection="field.collection"
								:datatype="field.datatype"
								:options="field.options"
								:value="item[junctionRelatedKey][field.field]"
								:values="item[junctionRelatedKey]"
							/>
						</div>
						<button
							v-if="!readonly"
							class="remove"
							@click.stop="deleteItem(item[junctionPrimaryKey])"
						>
							<v-icon name="close" />
						</button>
					</div>
				</draggable>
			</div>

			<v-notice v-else color="gray-subdued" icon="info">
				{{ $t('no_items_selected') }}
			</v-notice>

			<div v-if="!readonly" class="buttons">
				<v-button
					v-if="options.allow_create"
					type="button"
					:disabled="readonly"
					@click="startAddNewItem"
				>
					<v-icon name="add" left />
					{{ $t('add_new') }}
				</v-button>

				<v-button
					v-if="options.allow_select"
					type="button"
					:disabled="readonly"
					@click="selectExisting = true"
				>
					<v-icon name="playlist_add" left />
					{{ $t('select_existing') }}
				</v-button>
			</div>
		</template>

		<v-item-select
			v-if="selectExisting"
			:fields="visibleFieldNames"
			:collection="relation.junction.collection_one.collection"
			:filters="[]"
			:value="stagedSelection || selectionPrimaryKeys"
			@input="stageSelection"
			@done="closeSelection"
			@cancel="cancelSelection"
		/>

		<portal v-if="editItem" to="modal">
			<v-modal
				:title="addNew ? $t('creating_item') : $t('editing_item')"
				:buttons="{
					save: {
						text: $t('save'),
						color: 'accent'
					}
				}"
				@close="closeEditItem"
				@save="saveEditItem"
			>
				<div class="edit-modal-body">
					<v-form
						new-item
						:fields="relation.junction.collection_one.fields"
						:collection="relation.junction.collection_one.collection"
						:primary-key="
							(editItem[junctionRelatedKey] &&
								editItem[junctionRelatedKey][relatedPrimaryKeyField]) ||
								'+'
						"
						:values="editItem[junctionRelatedKey]"
						@stage-value="stageValue"
					/>
				</div>
			</v-modal>
		</portal>
	</div>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';
import { diff } from 'deep-object-diff';
import shortid from 'shortid';
import { get, find, orderBy, cloneDeep, mapValues, merge, difference } from 'lodash';

export default {
	name: 'InterfaceManyToMany',
	mixins: [mixin],
	data() {
		return {
			sort: {
				field: null,
				asc: true
			},

			selectExisting: false,
			editItem: false,
			addNew: null,

			dragging: false,

			items: null,
			loading: false,
			error: null,
			stagedSelection: null,

			initialValue: null
		};
	},

	computed: {
		// If the relationship has been configured or not
		relationshipSetup() {
			if (!this.relation) return false;
			return true;
		},

		// The fields that should be rendered in the modal / table
		visibleFields() {
			if (this.relationSetup === false) return [];
			if (!this.options.fields) return [];

			let visibleFieldNames;

			if (Array.isArray(this.options.fields)) {
				visibleFieldNames = this.options.fields.map(val => val.trim());
			}

			visibleFieldNames = this.options.fields.split(',').map(val => val.trim());

			// Fields in the related collection (not the JT)
			const relatedFields = this.relation.junction.collection_one.fields;
			const recursiveKey = get(this.relation, 'junction.field_one.field', null);

			return visibleFieldNames.map(name => {
				const fieldInfo = relatedFields[name];

				if (recursiveKey && name === recursiveKey) {
					fieldInfo.readonly = true;
				}

				return fieldInfo;
			});
		},

		visibleFieldNames() {
			return this.visibleFields.map(field => field.field);
		},

		// The name of the field that holds the primary key in the related (not JT) collection
		relatedPrimaryKeyField() {
			return find(this.relation.junction.collection_one.fields, { primary_key: true }).field;
		},

		selectionPrimaryKeys() {
			return this.items.map(
				item => item[this.junctionRelatedKey][this.relatedPrimaryKeyField]
			);
		},

		// Field in the junction table that holds the sort value in the junction table
		sortField() {
			const junctionTableFields = this.relation.collection_many.fields;
			const sortField = find(junctionTableFields, { type: 'sort' });
			return sortField;
		},

		// If the items can be manually sorted
		sortable() {
			return !!this.sortField;
		},

		manualSortActive() {
			return this.sort.field === '$manual';
		},

		// The key in the junction row that holds the data of the related item
		junctionRelatedKey() {
			return this.relation.junction.field_many.field;
		},

		junctionPrimaryKey() {
			return find(this.relation.junction.collection_many.fields, { primary_key: true }).field;
		},

		itemsSorted: {
			get() {
				if (this.sort.field === '$manual') {
					return orderBy(
						cloneDeep(this.items),
						item => item[this.sortField.field],
						this.sort.asc ? 'asc' : 'desc'
					);
				}

				return orderBy(
					cloneDeep(this.items),
					item => item[this.junctionRelatedKey][this.sort.field],
					this.sort.asc ? 'asc' : 'desc'
				);
			},
			set(newValue) {
				this.items = newValue.map((item, index) => {
					return {
						...item,
						[this.sortField.field]: index + 1
					};
				});
			}
		}
	},

	watch: {
		items(value, oldValue) {
			if (oldValue === null) return;
			this.emitValue(value);
		}
	},
	async created() {
		if (this.sortable) {
			this.sort.field = '$manual';
		} else {
			// Set the default sort column
			if (this.visibleFields && this.visibleFields.length > 0) {
				this.sort.field = this.visibleFields[0].field;
			}
		}

		await this.getInitialValue();

		// Set the initial set of items. Filter out any broken junction records
		this.items = (cloneDeep(this.initialValue) || []).filter(
			item => item[this.junctionRelatedKey]
		);
	},

	methods: {
		async getInitialValue() {
			const fields = [this.junctionPrimaryKey, this.relation.junction_field + '.*'];
			const response = await this.$api.getItems(this.relation.collection_many.collection, {
				fields,
				filter: {
					[this.relation.field_many.field]: this.primaryKey
				}
			});

			this.initialValue = response.data;
		},

		// Change the sort position to the provided field. If the same field is
		// changed, flip the sort order
		changeSort(fieldName) {
			if (this.sort.field === fieldName) {
				this.sort.asc = !this.sort.asc;
				return;
			}

			this.sort.asc = true;
			this.sort.field = fieldName;
			return;
		},

		startAddNewItem() {
			this.addNew = true;

			const relatedCollectionFields = this.relation.junction.collection_one.fields;
			const defaults = mapValues(relatedCollectionFields, field => field.default_value);
			const tempKey = '$temp_' + shortid.generate();

			if (defaults.hasOwnProperty(this.relatedPrimaryKeyField))
				delete defaults[this.relatedPrimaryKeyField];

			this.items = [
				...this.items,
				{
					[this.junctionPrimaryKey]: tempKey,
					[this.junctionRelatedKey]: defaults
				}
			];

			this.startEdit(tempKey);
		},

		// Save the made edits in the add new item modal
		stageValue({ field, value }) {
			this.$set(this.editItem[this.junctionRelatedKey], field, value);
		},

		toggleManualSort() {
			this.sort.field = '$manual';
			this.sort.asc = true;
		},

		async startEdit(primaryKey) {
			let values = cloneDeep(this.items.find(i => i[this.junctionPrimaryKey] === primaryKey));

			const isNewItem = typeof primaryKey === 'string' && primaryKey.startsWith('$temp_');

			// Fetch the values from the DB
			if (isNewItem === false) {
				const collection = this.relation.collection_many.collection;
				const primaryKeyName = this.relation.collection_many.fields.id.field;

				const res = await this.$api.getItem(collection, primaryKey, { fields: '*.*.*' });
				const item = res.data;

				values = merge({}, item, values);

				// Update the initialValue as well since the initialValue is used to get the Diff for POSTing
				this.initialValue = this.initialValue.map(item => {
					if (get(item, primaryKeyName) === primaryKey) {
						return cloneDeep(values);
					}
					return item;
				});
			}

			this.editItem = values;
		},

		saveEditItem() {
			const primaryKey = this.editItem[this.junctionPrimaryKey];

			this.items = this.items.map(item => {
				if (item[this.junctionPrimaryKey] === primaryKey) {
					return this.editItem;
				}

				return item;
			});

			this.editItem = null;
		},

		closeEditItem() {
			//If addNew is true and cancel is clicked, need to remove a last added blank item.
			if (this.addNew) {
				this.items.pop();
			}
			this.addNew = false;
			this.editItem = null;
		},

		stageSelection(primaryKeys) {
			this.stagedSelection = primaryKeys;
		},

		async closeSelection() {
			//When there is no change in selection and user click on done.
			if (!this.stagedSelection) {
				this.selectExisting = false;
				return;
			}

			const primaryKeys = this.stagedSelection || [];

			// Remove all the items from this.items that aren't selected anymore
			this.items = this.items.filter(item => {
				const primaryKey = item[this.junctionRelatedKey][this.relatedPrimaryKeyField];
				return primaryKeys.includes(primaryKey);
			});

			// Fetch all the newly selected items so we can render it in the table
			const itemPrimaryKeys = this.items.map(
				item => item[this.junctionRelatedKey][this.relatedPrimaryKeyField]
			);
			const newlyAddedItems = difference(primaryKeys, itemPrimaryKeys);

			if (newlyAddedItems.length > 0) {
				const res = await this.$api.getItem(
					this.relation.junction.collection_one.collection,
					newlyAddedItems.join(','),
					{
						fields: '*.*.*'
					}
				);

				const items = Array.isArray(res.data) ? res.data : [res.data];

				const newJunctionRecords = items.map(nested => {
					const tempKey = '$temp_' + shortid.generate();

					return {
						[this.junctionPrimaryKey]: tempKey,
						[this.junctionRelatedKey]: nested
					};
				});

				this.items = [...this.items, ...newJunctionRecords];
			}

			this.stagedSelection = null;
			this.selectExisting = false;
		},

		cancelSelection() {
			this.stagedSelection = null;
			this.selectExisting = null;
		},

		deleteItem(primaryKey) {
			this.items = this.items.filter(jr => {
				const jrPrimaryKey = jr[this.junctionPrimaryKey];
				return jrPrimaryKey !== primaryKey;
			});
		},

		emitValue(value) {
			value = cloneDeep(value);

			// This is the key in the nested related object that holds the parent item again
			const recursiveKey = get(this.relation, 'junction.field_one.field', null);

			const newValue = value
				.map(after => {
					const primaryKey = after[this.junctionPrimaryKey];

					// Check if the current item was saved before
					const before = this.initialValue.find(
						i => i[this.junctionPrimaryKey] === primaryKey
					);

					if (before) {
						const delta = diff(before, after);

						if (Object.keys(delta).length > 0) {
							const newVal = {
								[this.junctionPrimaryKey]: primaryKey,
								[this.junctionRelatedKey]: {
									[this.relatedPrimaryKeyField]:
										before[this.junctionRelatedKey][this.relatedPrimaryKeyField]
								}
							};

							// Just in case there's an edit in the deep-nested recursive copy of the parent item
							// delete it
							if (
								recursiveKey &&
								newVal[this.junctionRelatedKey].hasOwnProperty(recursiveKey)
							) {
								delete newVal[this.junctionRelatedKey][recursiveKey];
							}

							return merge({}, newVal, delta);
						} else {
							return null;
						}
					}

					// If the junction item didn't exist before yet:
					if (
						typeof after[this.junctionPrimaryKey] === 'string' &&
						after[this.junctionPrimaryKey].startsWith('$temp_')
					) {
						delete after[this.junctionPrimaryKey];
					}

					// If the new (before case was handled above) item's primary key field is set, then this is only the reference and
					// we should only send the primary key field to the API.
					// Otherwise the update is triggered by the API on the whole hierarchy
					if (
						after[this.junctionRelatedKey] &&
						after[this.junctionRelatedKey][this.relatedPrimaryKeyField]
					) {
						after[this.junctionRelatedKey] = {
							[this.relatedPrimaryKeyField]:
								after[this.junctionRelatedKey][this.relatedPrimaryKeyField]
						};
					}

					return after;
				})
				.filter(i => i);

			const savedPrimaryKeys = this.initialValue.map(jr => jr[this.junctionPrimaryKey]);
			const newPrimaryKeys = value.map(jr => jr[this.junctionPrimaryKey]);
			const deletedKeys = difference(savedPrimaryKeys, newPrimaryKeys);
			const deletedJunctionRows = deletedKeys.map(key => {
				return {
					[this.junctionPrimaryKey]: key,
					$delete: true
				};
			});

			this.$emit('input', [...newValue, ...deletedJunctionRows]);
		}
	}
};
</script>

<style lang="scss" scoped>
.table {
	background-color: var(--page-background-color);
	border: var(--input-border-width) solid var(--input-border-color);
	border-radius: var(--border-radius);
	border-spacing: 0;
	width: 100%;
	margin: 16px 0 24px;

	.header {
		height: var(--input-height);
		border-bottom: 2px solid var(--table-head-border-color);

		button {
			text-align: left;
			transition: color var(--fast) var(--transition);
		}
	}

	.row {
		display: flex;
		align-items: center;
		padding: 0 5px;
		color: var(--input-text-color);

		> div {
			padding: 3px 5px;
			flex-basis: 200px;
			max-width: 200px;
		}
	}

	.header .row {
		align-items: center;
		height: 40px;

		& > button {
			padding: 3px 5px 2px;
			flex-basis: 200px;
			max-width: 200px;
		}
	}

	.body {
		max-height: 275px;
		overflow-y: scroll;
		-webkit-overflow-scrolling: touch;

		.row {
			cursor: pointer;
			position: relative;
			height: 50px;
			border-bottom: 2px solid var(--table-row-border-color);

			&:hover {
				background-color: var(--highlight);
			}

			& div:last-of-type {
				flex-grow: 1;
			}

			button {
				color: var(--input-icon-color);
				transition: color var(--fast) var(--transition);

				&:hover {
					transition: none;
					color: var(--danger);
				}
			}
		}

		&.readonly {
			pointer-events: none;
		}
	}

	.sort-column {
		flex-basis: 36px !important;

		&.disabled i {
			color: var(--input-background-color-disabled);
			cursor: not-allowed;
		}
	}
}

.drag-handle {
	cursor: grab;
}

.dragging {
	cursor: grabbing !important;
}

.buttons {
	margin-top: 24px;
}

.buttons > * {
	display: inline-block;
}

.buttons > *:first-child {
	margin-right: 24px;
}

.edit-modal-body {
	padding: 30px 30px 60px 30px;
	background-color: var(--page-background-color);
	.form {
		grid-template-columns:
			[start] minmax(0, var(--form-column-width)) [half] minmax(0, var(--form-column-width))
			[full];
	}
}

.remove {
	position: absolute;
	right: 10px;
}
</style>
