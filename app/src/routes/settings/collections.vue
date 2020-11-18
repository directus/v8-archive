<template>
	<div class="collections">
		<v-header :breadcrumb="breadcrumb" :icon-link="`/${currentProjectKey}/settings`" settings>
			<template slot="buttons">
				<v-header-button
					key="add"
					icon="add"
					:label="$t('new')"
					icon-color="button-primary-text-color"
					background-color="button-primary-background-color"
					@click="addNew = true"
				/>
			</template>
		</v-header>
		<v-error
			v-if="items.length === 0"
			:title="$t('no_collections')"
			:body="$t('no_collections_body')"
			icon="error_outline"
		/>

		<div v-else class="table">
			<div class="header">
				<div class="row">
					<div class="cell icon type-table-head">
						<v-icon name="box" color="--input-icon-color" />
					</div>
					<div class="cell type-table-head">{{ $tc('collection', 2) }}</div>
				</div>
			</div>
			<div class="body">
				<router-link
					v-for="collection in items"
					:key="collection.collection"
					class="row"
					:class="collection.hidden ? 'subdue' : null"
					:to="collection.__link__"
				>
					<div class="cell icon">
						<v-icon :name="collection.icon || 'box'" color="--input-icon-color" />
					</div>
					<div class="cell name">
						{{ collection.collection }}
						<span v-if="collection.note" class="note">({{ collection.note }})</span>
					</div>
					<v-button
						v-if="collection.managed"
						class="managed"
						x-small
						background-color="--button-tertiary-background-color"
						color="--button-tertiary-text-color"
						hover-background-color="--danger"
						hover-color="--white"
						:loading="toManage.includes(collection.collection)"
						@click.native.stop.prevent="toggleManage(collection)"
					>
						{{ $t('dont_manage') }}
					</v-button>
					<v-button
						v-else
						class="not-managed"
						x-small
						:loading="toManage.includes(collection.collection)"
						@click.native.stop.prevent="toggleManage(collection)"
					>
						{{ $t('manage') }}
					</v-button>
				</router-link>
			</div>
		</div>

		<portal v-if="addNew" to="modal">
			<v-prompt
				v-model="newName"
				safe
				:confirm-text="$t('create')"
				:title="$t('create_collection')"
				:placeholder="$t('enter_collection_name')"
				:loading="adding"
				@cancel="addNew = false"
				@confirm="add"
			>
				<v-details title="Default fields" :open="true">
					<div class="advanced-form">
						<v-switch v-model="status" :label="$t('status')" />
						<v-switch v-model="sort" :label="$t('sort')" />
						<v-switch v-model="owner" :label="$t('owner')" />
						<v-switch v-model="createdOn" :label="$t('created_on')" />
						<v-switch v-model="modifiedBy" :label="$t('modified_by')" />
						<v-switch v-model="modifiedOn" :label="$t('modified_on')" />
					</div>
				</v-details>
			</v-prompt>
		</portal>

		<portal v-if="dontManage" to="modal">
			<v-confirm
				:message="$t('dont_manage_copy')"
				color="danger"
				:confirm-text="$t('dont_manage')"
				:loading="toManage.includes(dontManage.collection.collection)"
				@cancel="dontManage = null"
				@confirm="stopManaging"
			/>
		</portal>
		<v-info-sidebar wide>
			<span class="type-note">No settings</span>
		</v-info-sidebar>
	</div>
</template>

<script>
import { mapState } from 'vuex';

export default {
	name: 'SettingsCollections',
	metaInfo() {
		return {
			title: `${this.$t('settings')} | ${this.$t('settings_collections_fields')}`
		};
	},
	data() {
		return {
			addNew: false,
			newName: '',
			adding: false,
			status: true,
			sort: false,
			owner: true,
			createdOn: true,
			modifiedBy: false,
			modifiedOn: false,

			dontManage: null,
			toManage: []
		};
	},
	computed: {
		...mapState(['currentProjectKey']),
		items() {
			const collections = this.$store.state.collections || {};

			return Object.values(collections)
				.filter(collection => collection.collection.startsWith('directus_') === false)
				.map(collection => ({
					...collection,
					__link__: `/${this.currentProjectKey}/settings/collections/${collection.collection}`
				}));
		},
		breadcrumb() {
			return [
				{
					name: this.$t('settings'),
					path: `/${this.currentProjectKey}/settings`
				},
				{
					name: this.$t('collections_and_fields'),
					path: `/${this.currentProjectKey}/settings/collections`
				}
			];
		}
	},
	methods: {
		add() {
			this.adding = true;

			const id = this.$helpers.shortid.generate();
			this.$store.dispatch('loadingStart', { id });

			let fieldsToAdd = [
				{
					type: 'integer',
					datatype: 'INT',
					length: 15,
					field: 'id',
					interface: 'primary-key',
					auto_increment: true,
					primary_key: true,
					hidden_detail: true,
					hidden_browse: true
				}
			];
			let fieldsToDispatch = {
				id: {
					auto_increment: true,
					collection: this.newName,
					datatype: 'INT',
					default_value: null,
					field: 'id',
					group: null,
					hidden_detail: true,
					hidden_browse: true,
					interface: 'primary-key',
					length: '10',
					locked: 0,
					note: '',
					options: null,
					primary_key: true,
					readonly: 0,
					required: true,
					signed: false,
					sort: 1,
					translation: null,
					type: 'integer',
					unique: false,
					validation: null,
					width: 'full'
				}
			};

			if (this.status) {
				fieldsToAdd.push({
					type: 'status',
					datatype: 'VARCHAR',
					length: 20,
					field: 'status',
					interface: 'status',
					default_value: 'draft',
					width: 'full',
					required: true,
					options: {
						status_mapping: {
							published: {
								name: 'Published',
								value: 'published',
								text_color: 'white',
								background_color: 'accent',
								browse_subdued: false,
								browse_badge: true,
								soft_delete: false,
								published: true,
								required_fields: true
							},
							draft: {
								name: 'Draft',
								value: 'draft',
								text_color: 'white',
								background_color: 'blue-grey-100',
								browse_subdued: true,
								browse_badge: true,
								soft_delete: false,
								published: false,
								required_fields: false
							},
							deleted: {
								name: 'Deleted',
								value: 'deleted',
								text_color: 'white',
								background_color: 'red',
								browse_subdued: true,
								browse_badge: true,
								soft_delete: true,
								published: false,
								required_fields: false
							}
						}
					}
				});
				fieldsToDispatch.status = {
					collection: this.newName,
					field: 'status',
					datatype: 'VARCHAR',
					unique: false,
					primary_key: false,
					auto_increment: false,
					default_value: 'draft',
					note: null,
					signed: true,
					type: 'status',
					sort: 0,
					interface: 'status',
					hidden_detail: false,
					hidden_browse: false,
					required: true,
					options: {
						status_mapping: {
							published: {
								name: 'Published',
								value: 'published',
								text_color: 'white',
								background_color: 'accent',
								browse_subdued: false,
								browse_badge: true,
								soft_delete: false,
								published: true
							},
							draft: {
								name: 'Draft',
								value: 'draft',
								text_color: 'white',
								background_color: 'blue-grey-100',
								browse_subdued: true,
								browse_badge: true,
								soft_delete: false,
								published: false
							},
							deleted: {
								name: 'Deleted',
								value: 'deleted',
								text_color: 'white',
								background_color: 'red',
								browse_subdued: true,
								browse_badge: true,
								soft_delete: true,
								published: false
							}
						}
					},
					locked: false,
					translation: null,
					readonly: false,
					width: 'full',
					validation: null,
					group: null,
					length: '20'
				};
			}
			if (this.sort) {
				fieldsToAdd.push({
					type: 'sort',
					datatype: 'INT',
					field: 'sort',
					interface: 'sort',
					hidden_detail: true,
					hidden_browse: true,
					width: 'full'
				});
				fieldsToDispatch.sort = {
					collection: this.newName,
					field: 'sort',
					datatype: 'INT',
					unique: false,
					primary_key: false,
					auto_increment: false,
					default_value: null,
					note: null,
					signed: false,
					type: 'sort',
					sort: 0,
					interface: 'sort',
					hidden_detail: true,
					hidden_browse: true,
					required: false,
					options: null,
					locked: false,
					translation: null,
					readonly: false,
					width: 'full',
					validation: null,
					group: null,
					length: '10'
				};
			}
			if (this.owner) {
				fieldsToAdd.push({
					type: 'owner',
					datatype: 'INT',
					field: 'owner',
					interface: 'owner',
					options: {
						template: '{{first_name}} {{last_name}}',
						display: 'both'
					},
					readonly: true,
					hidden_detail: true,
					hidden_browse: true,
					width: 'full'
				});
				fieldsToDispatch.owner = {
					collection: this.newName,
					field: 'owner',
					datatype: 'INT',
					unique: false,
					primary_key: false,
					auto_increment: false,
					default_value: null,
					note: null,
					signed: false,
					type: 'owner',
					sort: 0,
					interface: 'owner',
					hidden_detail: true,
					hidden_browse: true,
					required: false,
					options: {
						template: '{{first_name}} {{last_name}}',
						display: 'both'
					},
					locked: false,
					translation: null,
					readonly: true,
					width: 'full',
					validation: null,
					group: null,
					length: '10'
				};
			}
			if (this.createdOn) {
				fieldsToAdd.push({
					type: 'datetime_created',
					datatype: 'DATETIME',
					field: 'created_on',
					interface: 'datetime-created',
					readonly: true,
					hidden_detail: true,
					hidden_browse: true,
					width: 'full'
				});
				fieldsToDispatch.created_on = {
					collection: this.newName,
					field: 'created_on',
					datatype: 'DATETIME',
					unique: false,
					primary_key: false,
					auto_increment: false,
					default_value: null,
					note: null,
					signed: true,
					type: 'datetime_created',
					sort: 0,
					interface: 'datetime-created',
					hidden_detail: true,
					hidden_browse: true,
					required: false,
					options: null,
					locked: false,
					translation: null,
					readonly: true,
					width: 'full',
					validation: null,
					group: null,
					length: null
				};
			}
			if (this.modifiedBy) {
				fieldsToAdd.push({
					type: 'user_updated',
					datatype: 'INT',
					field: 'modified_by',
					interface: 'user-updated',
					options: {
						template: '{{first_name}} {{last_name}}',
						display: 'both'
					},
					readonly: true,
					hidden_detail: true,
					hidden_browse: true,
					width: 'full'
				});
				fieldsToDispatch.modified_by = {
					collection: this.newName,
					field: 'modified_by',
					datatype: 'INT',
					unique: false,
					primary_key: false,
					auto_increment: false,
					default_value: null,
					note: null,
					signed: false,
					type: 'user_updated',
					sort: 0,
					interface: 'user-updated',
					hidden_detail: true,
					hidden_browse: true,
					required: false,
					options: {
						template: '{{first_name}} {{last_name}}',
						display: 'both'
					},
					locked: false,
					translation: null,
					readonly: true,
					width: 'full',
					validation: null,
					group: null,
					length: '10'
				};
			}
			if (this.modifiedOn) {
				fieldsToAdd.push({
					type: 'datetime_updated',
					datatype: 'DATETIME',
					field: 'modified_on',
					interface: 'datetime-updated',
					readonly: true,
					hidden_detail: true,
					hidden_browse: true,
					width: 'full'
				});
				fieldsToDispatch.modified_on = {
					collection: this.newName,
					field: 'modified_on',
					datatype: 'DATETIME',
					unique: false,
					primary_key: false,
					auto_increment: false,
					default_value: null,
					note: null,
					signed: true,
					type: 'datetime_updated',
					sort: 0,
					interface: 'datetime-updated',
					hidden_detail: true,
					hidden_browse: true,
					required: false,
					options: null,
					locked: false,
					translation: null,
					readonly: true,
					width: 'full',
					validation: null,
					group: null,
					length: null
				};
			}

			this.$api
				.createCollection(
					{
						collection: this.newName,
						hidden: 0,
						fields: fieldsToAdd
					},
					{
						fields: '*.*'
					}
				)
				.then(res => res.data)
				.then(collection => {
					this.$store.dispatch('loadingFinished', id);
					this.$store.dispatch('addCollection', {
						...collection,

						// This should ideally be returned from the API
						// https://github.com/directus/api/issues/207
						fields: fieldsToDispatch
					});
					this.$store.dispatch('getPermissions');
					this.$router.push(
						`/${this.currentProjectKey}/settings/collections/${this.newName}`
					);
				})
				.catch(error => {
					this.adding = false;
					this.$store.dispatch('loadingFinished', id);
					if (error) {
						// Use bit more descriptive error if possible
						const errors = {
							4: this.$t('collection_invalid_name')
						};
						this.$events.emit('error', {
							notify:
								error.code in errors
									? errors[error.code]
									: this.$t('something_went_wrong_body'),
							error
						});
					}
				});
		},
		toggleManage(collection) {
			if (collection.managed) {
				this.dontManage = collection;
			} else {
				return this.$api
					.updateItem('directus_collections', collection.collection, {
						managed: true
					})
					.then(() => {
						this.toManage.push(collection.collection);
						return this.$store.dispatch('getCollections');
					})
					.then(() => {
						this.$notify({
							title: this.$t('manage_started'),
							color: 'green',
							iconMain: 'check'
						});
					})
					.catch(error => {
						this.$events.emit('error', {
							notify: this.$t('something_went_wrong_body'),
							error
						});
					})
					.then(() => {
						this.toManage.splice(this.toManage.indexOf(collection.collection), 1);
					});
			}
		},
		stopManaging() {
			const dontManage = this.dontManage;
			this.toManage.push(dontManage.collection.collection);
			return this.$api
				.updateItem('directus_collections', dontManage.collection, {
					managed: false
				})
				.then(() => {
					return this.$store.dispatch('getCollections');
				})
				.then(() => {
					this.$notify({
						title: this.$t('manage_stopped'),
						color: 'green',
						iconMain: 'check'
					});
				})
				.catch(error => {
					this.$events.emit('error', {
						notify: this.$t('something_went_wrong_body'),
						error
					});
				})
				.then(() => {
					this.toManage.splice(
						this.toManage.indexOf(dontManage.collection.collection),
						1
					);
					this.dontManage = null;
				});
		}
	}
};
</script>

<style lang="scss" scoped>
.collections {
	padding: var(--page-padding) var(--page-padding) var(--page-padding-bottom);
}

.table {
	background-color: var(--page-background-color);
	position: relative;

	.row {
		display: flex;
		align-items: center;
		border-bottom: 2px solid var(--table-row-border-color);
		box-sizing: content-box;
		height: 48px;
		&.subdue {
			color: var(--note-text-color);
		}
	}

	.cell {
		flex-shrink: 0;
		padding-left: 12px;
		position: relative;
		overflow: hidden;
		max-height: 100%;
		&.icon {
			flex-basis: 40px;
		}
		&.name {
			font-family: 'Roboto Mono', monospace;
			max-width: calc(100% - 140px);
			padding-bottom: 2px; // Optical vertical alignment
			white-space: nowrap;
			overflow: hidden;
		}
		.note {
			// transition: opacity var(--fast) var(--transition);
			padding-left: 20px;
			opacity: 0;
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
		}
	}

	.header {
		position: relative;
		top: 0;
		height: var(--input-height);

		.row {
			height: 100%;
			border-bottom: 2px solid var(--table-head-border-color);
		}
	}

	a {
		text-decoration: none;

		&:hover {
			background-color: var(--highlight);
			.note {
				opacity: 1;
			}
		}
	}

	.v-button {
		position: absolute;
		right: 12px;
	}
}

.v-details {
	margin-top: 30px;
	margin-bottom: 0;

	.advanced-form {
		display: grid;
		grid-gap: 20px;
		grid-template-columns: 1fr 1fr;
	}
}
</style>
