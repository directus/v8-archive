<template>
	<not-found v-if="!collectionInfo" />
	<div v-else class="settings-fields">
		<v-header
			:breadcrumb="breadcrumb"
			:icon-link="`/${currentProjectKey}/settings/collections`"
			settings
		>
			<template slot="buttons">
				<v-header-button
					key="delete"
					icon="delete_outline"
					background-color="danger"
					icon-color="white"
					hover-color="danger-dark"
					:label="$t('delete')"
					@click="confirmRemove = true"
				/>
				<v-header-button
					key="save"
					icon="check"
					background-color="button-primary-background-color"
					icon-color="button-primary-text-color"
					:loading="saving"
					:disabled="Object.keys(edits).length === 0"
					:label="$t('save')"
					@click="save"
				/>
			</template>
		</v-header>

		<label class="type-label">{{ $tc('field', 2) }}</label>
		<v-notice color="warning" icon="warning">{{ $t('fields_are_saved_instantly') }}</v-notice>
		<div class="table">
			<div class="header">
				<div class="row">
					<div class="drag"><v-icon name="swap_vert" /></div>
					<div>{{ $tc('field', 1) }}</div>
					<div>{{ $tc('interface', 1) }}</div>
				</div>
			</div>
			<div class="body" :class="{ dragging }">
				<draggable v-model="fields" @start="startSort" @end="saveSort">
					<div v-for="field in fields" :key="field.field" class="row">
						<div class="drag"><v-icon name="drag_handle" /></div>
						<div
							class="inner row"
							:style="{ cursor: field.interface ? 'inherit' : 'default' }"
							@click.stop="field.interface ? startEditingField(field) : false"
						>
							<div
								class="monospace"
								v-tooltip="field.field.length > 25 ? field.field : null"
							>
								{{ field.field }}
							</div>
							<div>
								{{
									$store.state.extensions.interfaces[field.interface] &&
										$store.state.extensions.interfaces[field.interface].name
								}}
								<v-button
									v-if="!field.interface"
									class="not-managed"
									:loading="toManage.includes(field.field)"
									@click="manageField(field)"
								>
									{{ $t('manage') }}
								</v-button>
							</div>
						</div>
						<v-contextual-menu
							v-if="canDuplicate(field.interface) || fields.length > 1"
							class="more-options"
							placement="left-start"
							:options="fieldOptions(field)"
							@click="fieldOptionsClicked(field, $event)"
						></v-contextual-menu>
					</div>
				</draggable>
			</div>
		</div>

		<v-button class="new-field" @click="startEditingField({})">
			{{ $t('new_field') }}
		</v-button>

		<v-form
			v-if="fields"
			:fields="directusFields"
			:values="values"
			collection="directus_collections"
			@stage-value="stageValue"
		/>

		<portal v-if="confirmRemove" to="modal">
			<v-confirm
				color="danger"
				:message="$t('delete_collection_are_you_sure')"
				:confirm-text="$t('delete')"
				@cancel="confirmRemove = false"
				@confirm="remove"
			/>
		</portal>

		<portal v-if="confirmFieldRemove" to="modal">
			<v-confirm
				color="danger"
				:message="$t('delete_field_are_you_sure', { field: fieldToBeRemoved })"
				:confirm-text="$t('delete')"
				@cancel="confirmFieldRemove = false"
				@confirm="removeField(fieldToBeRemoved)"
			/>
		</portal>

		<v-field-setup
			v-if="editingField"
			:field-info="fieldBeingEdited"
			:collection-info="collectionInfo"
			:saving="fieldSaving"
			@close="editingField = false"
			@save="setFieldSettings"
		/>

		<v-field-duplicate
			v-if="duplicatingField"
			:field-information="fieldBeingDuplicated"
			:collection-information="collectionInfo"
			@close="duplicatingField = false"
			@save="duplicateFieldSettings"
		/>
		<v-info-sidebar wide>
			<span class="type-note">No settings</span>
		</v-info-sidebar>
	</div>
</template>

<script>
import { datatypes } from '../../type-map';
import { keyBy } from 'lodash';
import formatTitle from '@directus/format-title';
import shortid from 'shortid';
import store from '../../store/';
import api from '../../api.js';
import NotFound from '../not-found.vue';
import VFieldSetup from '../../components/field-setup.vue';
import VFieldDuplicate from '../../components/field-duplicate.vue';
import { mapState } from 'vuex';

export default {
	name: 'SettingsFields',
	metaInfo() {
		return {
			title: `${this.$t('settings')} | ${this.$t('editing', {
				collection: this.$helpers.formatTitle(this.collection)
			})}`
		};
	},
	components: {
		NotFound,
		VFieldSetup,
		VFieldDuplicate
	},
	props: {
		collection: {
			type: String,
			required: true
		}
	},
	data() {
		return {
			toManage: [],
			duplicateInterfaceBlacklist: [
				'primary-key',
				'many-to-many',
				'one-to-many',
				'many-to-one',
				'sort'
			],
			fieldSaving: false,
			saving: false,
			dragging: false,

			fields: null,
			directusFields: null,

			notFound: false,
			error: false,

			confirmRemove: false,
			confirmRemoveLoading: false,

			confirmNavigation: false,
			leavingTo: '',

			edits: {},

			fieldBeingEdited: null,
			fieldToBeRemoved: null,
			confirmFieldRemove: false,

			fieldBeingDuplicated: null,
			duplicatingField: false,

			editingField: false
		};
	},
	computed: {
		...mapState(['currentProjectKey']),
		breadcrumb() {
			return [
				{
					name: this.$t('settings'),
					path: `/${this.currentProjectKey}/settings`
				},
				{
					name: this.$t('collections_and_fields'),
					path: `/${this.currentProjectKey}/settings/collections`
				},
				{
					name: this.$helpers.formatCollection(this.collection),
					path: `/${this.currentProjectKey}/settings/collections/${this.collection}`
				}
			];
		},
		collectionInfo() {
			return this.$store.state.collections[this.collection];
		},
		fieldsWithSort() {
			return this.fields.map((field, index) => ({
				...field,
				sort: index + 1
			}));
		},
		values() {
			return {
				...this.collectionInfo,
				...this.edits
			};
		}
	},
	methods: {
		manageField(field) {
			this.toManage.push(field.field);
			const databaseVendor = this.$store.state.serverInfo.databaseVendor;
			const suggestedInterface = datatypes[databaseVendor][field.datatype].fallbackInterface;
			const fieldInfo = {
				field: field.field,
				sort: field.sort,
				interface: suggestedInterface
			};
			const result = {
				fieldInfo,
				relation: null
			};
			this.setFieldSettings(result).then(async () => {
				await this.$store.dispatch('getCollections');
				field.interface = suggestedInterface;
				this.toManage.splice(this.toManage.indexOf(field.field), 1);
			});
		},
		remove() {
			const id = this.$helpers.shortid.generate();
			this.$store.dispatch('loadingStart', { id });

			this.$api
				.deleteCollection(this.collection)
				.then(() => {
					this.$store.dispatch('loadingFinished', id);
					this.$store.dispatch('removeCollection', this.collection);
					this.$notify({
						title: this.$t('collection_removed'),
						color: 'green',
						iconMain: 'check'
					});
					this.$router.push(`/${this.currentProjectKey}/settings/collections`);
				})
				.catch(error => {
					this.$store.dispatch('loadingFinished', id);
					this.$events.emit('error', {
						notify: this.$t('something_went_wrong_body'),
						error
					});
				});
		},
		save() {
			this.saving = true;

			const id = this.$helpers.shortid.generate();
			this.$store.dispatch('loadingStart', { id });

			this.$api
				.updateCollection(this.collection, this.edits)
				.then(() => {
					this.$notify({
						title: this.$t('collection_updated'),
						color: 'green',
						iconMain: 'check'
					});
					this.$store.dispatch('loadingFinished', id);
					this.saving = false;
					this.$store.dispatch('updateCollection', {
						collection: this.collection,
						edits: this.edits
					});
					this.$router.push(`/${this.currentProjectKey}/settings/collections`);
				})
				.catch(error => {
					this.saving = false;
					this.$store.dispatch('loadingFinished', id);
					this.$events.emit('error', {
						notify: this.$t('something_went_wrong_body'),
						error
					});
				});
		},
		stageValue({ field, value }) {
			if (value === this.collectionInfo[field]) {
				this.$delete(this.edits, field);
				return;
			}

			this.$set(this.edits, field, value);
		},
		canDuplicate(fieldInterface) {
			if (!fieldInterface) {
				return false;
			}
			return this.duplicateInterfaceBlacklist.includes(fieldInterface) === false;
		},
		duplicateFieldSettings({ fieldInfo, collection }) {
			const requests = [];

			const id = this.$helpers.shortid.generate();
			this.$store.dispatch('loadingStart', { id });

			fieldInfo.collection = collection;
			requests.push(this.$api.createField(collection, fieldInfo));

			return Promise.all(requests)
				.then(([fieldRes]) => ({
					savedFieldInfo: fieldRes.data
				}))
				.then(({ savedFieldInfo }) => {
					this.$store.dispatch('loadingFinished', id);

					if (this.collection === collection) {
						this.fields = [...this.fields, savedFieldInfo];
					}
					this.$store.dispatch('addField', {
						collection: collection,
						field: savedFieldInfo
					});
					this.$notify({
						title: this.$t('field_created', {
							field: this.$helpers.formatField(fieldInfo.field, fieldInfo.collection)
						}),
						color: 'green',
						iconMain: 'check'
					});
				})
				.then(() => {
					this.duplicatingField = false;
					this.fieldBeingDuplicated = null;
				})
				.catch(error => {
					this.$store.dispatch('loadingFinished', id);
					this.$events.emit('error', {
						notify: this.$t('something_went_wrong_body'),
						error
					});
				});
		},

		async setFieldSettings({ fieldInfo, relation }) {
			this.fieldSaving = true;

			const existingField = this.$store.state.collections[
				this.collection
			].fields.hasOwnProperty(fieldInfo.field);

			const id = this.$helpers.shortid.generate();
			this.$store.dispatch('loadingStart', { id });

			try {
				if (existingField) {
					const { data: savedFieldInfo } = await this.$api.updateField(
						this.collection,
						fieldInfo.field,
						fieldInfo
					);

					this.fields = this.fields.map(field => {
						if (field.id === savedFieldInfo.id) return savedFieldInfo;
						return field;
					});

					this.$notify({
						title: this.$t('field_updated'),
						color: 'green',
						iconMain: 'check'
					});
				} else {
					const { data: savedFieldInfo } = await this.$api.createField(
						this.collection,
						fieldInfo
					);

					this.fields = [...this.fields, savedFieldInfo];

					this.$notify({
						title: this.$t('field_created'),
						color: 'green',
						iconMain: 'check'
					});
				}

				this.$store.dispatch('getCollections');

				if (relation) {
					const saveRelation = async relation => {
						const existingRelation = relation && relation.id != null;

						if (existingRelation) {
							const { data: updatedRelation } = await this.$api.updateRelation(
								relation.id,
								relation
							);
							this.$store.dispatch('updateRelation', updatedRelation);
						} else {
							const { data: newRelation } = await this.$api.createRelation(relation);
							this.$store.dispatch('addRelation', newRelation);
						}
					};

					if (Array.isArray(relation)) {
						for (let relationInfo of relation) {
							saveRelation(relationInfo);
						}
					} else {
						saveRelation(relation);
					}
				}

				this.editingField = false;
				this.fieldBeingEdited = null;
				this.fieldSaving = false;
				this.$store.dispatch('loadingFinished', id);
			} catch (error) {
				this.fieldSaving = false;
				this.$store.dispatch('loadingFinished', id);
				this.$events.emit('error', {
					notify: this.$t('something_went_wrong_body'),
					error
				});
			}
		},
		fieldOptions(field) {
			return [
				{
					text: this.$t('duplicate'),
					icon: 'control_point_duplicate',
					disabled: this.duplicateInterfaceBlacklist.includes(field.interface)
				},
				{
					text: this.$t('delete'),
					icon: 'delete_outline'
				}
			];
		},
		fieldOptionsClicked(field, option) {
			switch (option) {
				case 0:
					this.duplicateField(field);
					break;
				case 1:
					this.warnRemoveField(field.field);
					break;
				default:
			}
		},
		duplicateField(field) {
			this.fieldBeingDuplicated = field;
			this.duplicatingField = true;
		},
		startEditingField(field) {
			this.fieldBeingEdited = field;
			this.editingField = true;
		},
		warnRemoveField(fieldName) {
			this.fieldToBeRemoved = fieldName;
			this.confirmFieldRemove = true;
		},
		removeField(fieldName) {
			this.removingField = true;

			const id = this.$helpers.shortid.generate();
			this.$store.dispatch('loadingStart', { id });

			this.$api
				.deleteField(this.collection, fieldName)
				.then(() => {
					this.$store.dispatch('loadingFinished', id);
					this.fields = this.fields.filter(({ field }) => field !== fieldName);
					this.removingField = false;
					this.fieldToBeRemoved = null;
					this.confirmFieldRemove = false;
					this.$notify({
						title: this.$t('field_removed'),
						color: 'green',
						iconMain: 'check'
					});
					this.$store.dispatch('removeField', {
						collection: this.collection,
						field: fieldName
					});
				})
				.catch(error => {
					this.$store.dispatch('loadingFinished', id);
					this.$events.emit('error', {
						notify: this.$t('something_went_wrong_body'),
						error
					});
				});
		},
		startSort() {
			this.dragging = true;
		},
		saveSort() {
			this.dragging = false;

			const fieldUpdates = this.fieldsWithSort.map(field => ({
				field: field.field,
				sort: field.sort
			}));

			const id = this.$helpers.shortid.generate();
			this.$store.dispatch('loadingStart', { id });

			this.$api.api
				.patch(`/fields/${this.collection}`, fieldUpdates, {
					activity_skip: 1
				})
				.then(res => res.data)
				.then(fields => {
					this.$store.dispatch('loadingFinished', id);
					this.$store.dispatch('updateFields', {
						collection: this.collection,
						updates: fieldUpdates
					});
					this.fields = fields;
				})
				.catch(error => {
					this.$store.dispatch('loadingFinished', id);
					this.$events.emit('error', {
						notify: this.$t('something_went_wrong_body'),
						error
					});
				});
		}
	},
	beforeRouteEnter(to, from, next) {
		const { collection } = to.params;

		const id = shortid.generate();
		store.dispatch('loadingStart', { id });

		return Promise.all([
			api.getFields('directus_collections'),
			api.getFields(collection, {
				sort: 'sort'
			})
		])
			.then(([directusRes, fieldsRes]) => ({
				directusFields: directusRes.data,
				fields: fieldsRes.data
			}))
			.then(({ directusFields, fields }) => {
				store.dispatch('loadingFinished', id);
				next(vm => {
					vm.$data.directusFields = keyBy(
						directusFields.map(field => ({
							...field,
							name: formatTitle(field.field),
							note: field.note
						})),
						'field'
					);
					delete vm.$data.directusFields.note.note;
					vm.$data.directusFields.note.options = {
						placeholder: vm.$t('note_note')
					};

					vm.$data.fields = fields
						.map(field => ({
							...field,
							name: formatTitle(field.field)
						}))
						.sort((a, b) => {
							if (a.sort == b.sort) return 0;
							if (a.sort === null) return 1;
							if (b.sort === null) return -1;
							return a.sort > b.sort ? 1 : -1;
						});
				});
			})
			.catch(error => {
				store.dispatch('loadingFinished', id);
				next(vm => {
					vm.$data.error = error;
				});
			});
	}
};
</script>

<style lang="scss" scoped>
.settings-fields {
	padding: var(--page-padding-top) var(--page-padding) var(--page-padding-bottom);
}

h2 {
	margin-bottom: 20px;

	&:not(:first-of-type) {
		margin-top: 60px;
	}
}

.table {
	background-color: var(--page-background-color);
	border: var(--input-border-width) solid var(--input-border-color);
	border-radius: var(--border-radius);
	border-spacing: 0;
	width: 100%;
	max-width: 632px;
	margin: 10px 0 20px;

	.header {
		border-bottom: 2px solid var(--table-head-border-color);
		height: calc(var(--input-height) - 2px); // -2px, border on container
		.row {
			height: calc(var(--input-height) - 2px); // -2px, border on container
		}
	}

	.row {
		display: flex;
		align-items: center;

		> div {
			padding: 5px 5px;

			&:not(.drag):not(.more-options) {
				flex-basis: 260px;
			}
		}
	}

	.inner.row {
		flex-grow: 1;
		overflow: hidden;

		> div {
			padding: 0;
		}
	}

	.sortable-drag {
		opacity: 0;
	}

	.dragging .sortable-chosen,
	.sortable-chosen:active {
		background-color: var(--highlight) !important;
	}

	.body {
		.dragging .row.link:hover {
			background-color: var(--page-background-color);
		}

		.row {
			cursor: pointer;
			position: relative;
			height: calc(var(--input-height) - 2px); // -2px, border on container
			border-bottom: 2px solid var(--table-row-border-color);

			&.inner,
			&:last-of-type {
				border-bottom: none;
			}

			&:hover {
				background-color: var(--highlight);
			}
		}

		.drag {
			user-select: none;
			cursor: -webkit-grab;
			color: var(--input-border-color);

			&:hover {
				color: var(--input-border-color-hover);
			}
		}

		.monospace {
			font-family: 'Roboto Mono', monospace;
			overflow: hidden;
			text-overflow: ellipsis;
			margin-right: 8px;
		}
	}
}

.new-field {
	margin-bottom: 40px;
}

.more-options {
	position: absolute;
	right: 0;
	top: 50%;
	transform: translateY(-50%);

	i {
		color: var(--blue-grey-200);
		transition: color var(--fast) var(--transition);
	}

	&:hover {
		i {
			transition: none;
			color: var(--blue-grey-400);
		}
	}
}

em.note {
	color: var(--note-text-color);
	margin-top: var(--input-note-margin);
	margin-bottom: var(--form-vertical-gap);
	display: block;
}

label.type-label {
	margin-bottom: var(--input-label-margin);
	text-transform: none;
}

.ctx-menu {
	list-style: none;
	padding: 0;
	width: 136px;

	li {
		display: block;
	}

	i {
		color: var(--blue-grey-300);
		margin-right: 8px;
		transition: color var(--fast) var(--transition);
	}

	button {
		display: flex;
		align-items: center;
		padding: 4px;
		color: var(--blue-grey-400);
		width: 100%;
		height: 100%;
		transition: color var(--fast) var(--transition);
		&:disabled,
		&[disabled] {
			color: var(--blue-grey-200);
			i {
				color: var(--blue-grey-200);
			}
		}
		&:not(:disabled):not(&[disabled]):hover {
			color: var(--blue-grey-900);
			transition: none;
			i {
				color: var(--blue-grey-900);
				transition: none;
			}
		}
	}
}

button {
	&.not-managed {
		padding: 4px 8px;
		border-radius: var(--border-radius);
		background-color: var(--button-primary-background-color);
		color: var(--button-primary-text-color);

		min-width: auto;
		height: auto;
		border: 0;

		&:hover {
			background-color: var(--button-primary-background-color-hover);
			color: var(--button-primary-text-color);
		}
	}
}

.optional {
	color: var(--blue-grey-200);
}
</style>
