<template>
	<v-not-found v-if="notFound" />
	<div v-else class="route-item-listing">
		<v-header
			info-toggle
			:item-detail="false"
			:breadcrumb="breadcrumb"
			:icon="breadcrumbIcon"
			:settings="collection === 'directus_webhooks'"
			:title="currentBookmark && currentBookmark.title"
			:icon-link="
				collection === 'directus_webhooks' ? `/${currentProjectKey}/settings/` : null
			"
		>
			<template slot="title">
				<button
					:class="currentBookmark ? 'active' : null"
					:disabled="currentBookmark"
					class="bookmark"
					@click="bookmarkModal = true"
				>
					<v-icon
						:color="
							currentBookmark
								? '--input-background-color-active'
								: '--input-border-color'
						"
						:name="currentBookmark ? 'bookmark' : 'bookmark_border'"
					/>
				</button>
			</template>
			<v-search-filter
				v-show="selection && selection.length === 0 && !emptyCollection"
				:filters="filters"
				:search-query="searchQuery"
				:field-names="filterableFieldNames"
				:collection-name="collection.name"
				:placeholder="resultCopy"
				@filter="updatePreferences('filters', $event)"
				@search="updatePreferences('search_query', $event)"
				@clear-filters="clearFilters"
			/>
			<template slot="buttons">
				<v-header-button
					v-if="editButton && !activity"
					key="edit"
					icon="mode_edit"
					background-color="warning"
					icon-color="white"
					hover-color="warning-dark"
					:disabled="!editButtonEnabled"
					:label="$t('batch')"
					:to="batchURL"
				/>
				<v-header-button
					v-if="deleteButton && !activity"
					key="delete"
					icon="delete_outline"
					icon-color="white"
					background-color="danger"
					hover-color="danger-dark"
					:disabled="!deleteButtonEnabled"
					:label="$t('delete')"
					@click="confirmRemove = true"
				/>
				<v-header-button
					v-if="addButton && !activity"
					key="add"
					icon="add"
					icon-color="button-primary-text-color"
					background-color="button-primary-background-color"
					:label="$t('new')"
					:to="createLink"
				/>
			</template>
		</v-header>

		<v-items
			v-if="preferences"
			ref="listing"
			:collection="collection"
			:filters="filters"
			:search-query="searchQuery"
			:view-query="viewQuery"
			:view-type="viewType"
			:view-options="viewOptions"
			:selection="!activity ? selection : null"
			links
			@fetch="setMeta"
			@options="setViewOptions"
			@select="selection = $event"
			@query="setViewQuery"
		/>

		<v-info-sidebar v-if="preferences">
			<template slot="system">
				<div class="layout-picker">
					<select
						:value="viewType"
						@input="updatePreferences('view_type', $event.target.value)"
					>
						<option v-for="(name, val) in layoutNames" :key="val" :value="val">
							{{ name }}
						</option>
					</select>
					<div class="preview">
						<v-icon :name="layoutIcons[viewType]" color="--sidebar-text-color" />
						<span class="label">{{ layoutNames[viewType] }}</span>
						<v-icon name="expand_more" color="--sidebar-text-color" />
					</div>
				</div>
			</template>
			<v-ext-layout-options
				:key="`${collection}-${viewType}`"
				class="layout-options"
				:type="viewType"
				:collection="collection"
				:fields="keyBy(fields, 'field')"
				:view-options="viewOptions"
				:view-query="viewQuery"
				:selection="selection"
				:primary-key-field="primaryKeyField"
				link="__link__"
				@query="setViewQuery"
				@options="setViewOptions"
			/>
		</v-info-sidebar>
		<v-info-sidebar v-else wide>
			<span class="type-note">No settings</span>
		</v-info-sidebar>

		<portal v-if="confirmRemove" to="modal">
			<v-confirm
				:message="
					$tc('batch_delete_confirm', selection.length, {
						count: selection.length
					})
				"
				color="danger"
				:confirm-text="$t('delete')"
				@cancel="confirmRemove = false"
				@confirm="remove"
			/>
		</portal>

		<portal v-if="bookmarkModal" to="modal">
			<v-create-bookmark
				:preferences="preferences"
				@close="closeBookmark"
			></v-create-bookmark>
		</portal>
	</div>
</template>

<script>
import shortid from 'shortid';
import store from '../store/';
import VSearchFilter from '../components/search-filter/search-filter.vue';
import VCreateBookmark from '../components/bookmarks/create-bookmark.vue';
import VNotFound from './not-found.vue';
import { mapState } from 'vuex';
import { isEqual, isEmpty, isNil, find, findIndex, keyBy } from 'lodash';

import api from '../api';

export default {
	name: 'Items',
	metaInfo() {
		return {
			title: this.$helpers.formatTitle(this.collection)
		};
	},
	components: {
		VSearchFilter,
		VNotFound,
		VCreateBookmark
	},
	data() {
		return {
			selection: [],
			meta: null,
			preferences: null,
			confirmRemove: false,
			bookmarkModal: false,
			notFound: false
		};
	},
	computed: {
		...mapState(['currentProjectKey']),
		activity() {
			return this.collection === 'directus_activity';
		},
		breadcrumbIcon() {
			if (this.collection === 'directus_webhooks') return 'arrow_back';
			return this.collectionInfo?.icon || 'box';
		},
		createLink() {
			if (this.collection === 'directus_webhooks') {
				return `/${this.currentProjectKey}/settings/webhooks/+`;
			}

			if (this.collection.startsWith('directus_')) {
				return `/${this.currentProjectKey}/${this.collection.substr(9)}/+`;
			}

			return `/${this.currentProjectKey}/collections/${this.collection}/+`;
		},
		breadcrumb() {
			if (this.collection === 'directus_users') {
				return [
					{
						name: this.$t('user_directory'),
						path: `/${this.currentProjectKey}/users`
					}
				];
			}

			if (this.collection === 'directus_webhooks') {
				return [
					{
						name: this.$t('settings'),
						path: `/${this.currentProjectKey}/settings`
					},
					{
						name: this.$t('settings_webhooks'),
						path: `/${this.currentProjectKey}/settings/webhooks`
					}
				];
			}

			if (this.collection === 'directus_files') {
				return [
					{
						name: this.$t('file_library'),
						path: `/${this.currentProjectKey}/files`
					}
				];
			}

			if (this.collection.startsWith('directus_')) {
				return [
					{
						name: this.$helpers.formatTitle(this.collection.substr(9)),
						path: `/${this.currentProjectKey}/${this.collection.substring(9)}`
					}
				];
			} else {
				return [
					{
						name: this.$tc('collection', 2),
						path: `/${this.currentProjectKey}/collections`
					},
					{
						name: this.$helpers.formatCollection(this.collection),
						path: `/${this.currentProjectKey}/collections/${this.collection}`
					}
				];
			}
		},
		fields() {
			const fields = this.$store.state.collections[this.collection].fields;
			const fieldsArray = Object.values(fields).map(field => ({
				...field,
				name: this.$helpers.formatField(field.field, field.collection)
			}));

			//Filter out hidden_browser items.
			let filteredFields = fieldsArray.filter(field => field.hidden_browse !== true);

			return filteredFields;
		},
		batchURL() {
			return `/${this.currentProjectKey}/collections/${this.collection}/${this.selection
				.map(item => item[this.primaryKeyField])
				.join(',')}`;
		},
		currentBookmark() {
			if (!this.preferences) return;

			const bookmarks = this.$store.state.bookmarks;
			const preferences = {
				collection: this.preferences.collection,
				search_query: this.preferences.search_query,
				filters: this.preferences.filters,
				view_options: this.preferences.view_options,
				view_type: this.preferences.view_type,
				view_query: this.preferences.view_query
			};
			const currentBookmark = bookmarks.filter(bookmark => {
				const bookmarkPreferences = {
					collection: bookmark.collection,
					search_query: bookmark.search_query,
					filters: bookmark.filters,
					view_options: bookmark.view_options,
					view_type: bookmark.view_type,
					view_query: bookmark.view_query
				};
				return isEqual(bookmarkPreferences, preferences);
			})[0];
			return currentBookmark || null;
		},
		collection() {
			if (this.$route.path.endsWith('webhooks')) return 'directus_webhooks';
			return this.$route.params.collection;
		},
		collectionInfo() {
			return this.$store.state.collections[this.collection];
		},
		emptyCollection() {
			return (this.meta && this.meta.total_count === 0) || false;
		},
		filters() {
			if (!this.preferences) return [];
			return this.preferences.filters || [];
		},
		searchQuery() {
			if (!this.preferences) return '';
			return this.preferences.search_query || '';
		},
		viewType() {
			if (!this.preferences) return 'tabular';
			return this.preferences.view_type || 'tabular';
		},
		viewQuery() {
			if (!this.preferences) return {};

			// `Fields` computed property return the fields which need to displayed. Here we want all fields.
			let fields = this.$store.state.collections[this.collection].fields;
			fields = Object.values(fields).map(field => ({
				...field,
				name: this.$helpers.formatField(field.field, field.collection)
			}));

			const viewQuery =
				(this.preferences.view_query && this.preferences.view_query[this.viewType]) || {};

			// Filter out the fieldnames of fields that don't exist anymore
			// Sorting / querying fields that don't exist anymore will return
			// a 422 in the API and brick the app

			const collectionFieldNames = fields.map(f => f.field);

			if (viewQuery.fields) {
				viewQuery.fields = viewQuery.fields
					.split(',')
					.filter(fieldName => collectionFieldNames.includes(fieldName))
					.join(',');
			}

			if (viewQuery.sort) {
				// If the sort is descending, the fieldname starts with -
				// The fieldnames in the array of collection field names don't have this
				// which is why we have to take it out.
				const sortFieldName = viewQuery.sort.startsWith('-')
					? viewQuery.sort.substring(1)
					: viewQuery.sort;

				if (collectionFieldNames.includes(sortFieldName) === false) {
					viewQuery.sort = this.primaryKeyField;
				}
			}

			return viewQuery;
		},
		viewOptions() {
			if (!this.preferences) return {};
			return (
				(this.preferences.view_options && this.preferences.view_options[this.viewType]) ||
				{}
			);
		},
		resultCopy() {
			if (!this.meta || !this.preferences) return this.$t('loading');

			const isFiltering =
				!isEmpty(this.preferences.filters) ||
				(!isNil(this.preferences.search_query) && this.preferences.search_query.length > 0);

			// We're showing the collection total count, until we hit the last page of infinite scrolling.
			// At that point, we'll rely on the local count that's being set by the items.vue child component
			let count = this.meta.filter_count;

			if (this.meta.result_count < this.$store.state.settings.values.default_limit) {
				count = this.meta.local_count;
			}

			return isFiltering
				? this.$tc('item_count_filter', count, {
						count: this.$n(count)
				  })
				: this.$tc('item_count', count, {
						count: this.$n(count)
				  });
		},
		filterableFieldNames() {
			return this.fields.filter(field => field.datatype).map(field => field.field);
		},
		layoutNames() {
			if (!this.$store.state.extensions.layouts) return {};
			const translatedNames = {};
			Object.keys(this.$store.state.extensions.layouts).forEach(id => {
				translatedNames[id] = this.$store.state.extensions.layouts[id].name;
			});
			return translatedNames;
		},
		layoutIcons() {
			if (!this.$store.state.extensions.layouts) return {};
			const icons = {};
			Object.keys(this.$store.state.extensions.layouts).forEach(id => {
				icons[id] = this.$store.state.extensions.layouts[id].icon;
			});
			return icons;
		},
		statusField() {
			const fields = this.$store.state.collections[this.collection].fields;
			if (!fields) return null;
			let fieldsObj = find(fields, { type: 'status' });
			return fieldsObj && fieldsObj.field ? fieldsObj.field : null;
		},

		// Get the status name of the value that's marked as soft delete
		// This will make the delete button update the item to the hidden status
		// instead of deleting it completely from the database
		softDeleteStatus() {
			if (!this.collectionInfo.status_mapping || !this.statusField) return null;

			const statusKeys = Object.keys(this.collectionInfo.status_mapping);
			const index = findIndex(Object.values(this.collectionInfo.status_mapping), {
				soft_delete: true
			});
			return statusKeys[index];
		},

		userCreatedField() {
			if (!this.fields) return null;

			return (
				find(Object.values(this.fields), field => field.type.toLowerCase() === 'owner') ||
				{}
			).field;
		},
		primaryKeyField() {
			const fields = this.$store.state.collections[this.collection].fields;
			if (!fields) return null;
			let fieldsObj = find(fields, { primary_key: true });
			return fieldsObj && fieldsObj.field ? fieldsObj.field : null;
		},
		permissions() {
			return this.$store.state.permissions;
		},
		permission() {
			return this.permissions[this.collection];
		},
		addButton() {
			if (this.$store.state.currentUser.admin) return true;

			if (this.statusField) {
				return (
					Object.values(this.permission.statuses).some(
						permission => permission.create === 'full'
					) || this.permission.$create.create === 'full'
				);
			}

			return this.permission.create === 'full';
		},
		deleteButton() {
			if (!this.selection) return false;
			if (this.selection.length === 0) return false;
			return true;
		},
		deleteButtonEnabled() {
			if (this.$store.state.currentUser.admin) return true;
			const currentUserID = this.$store.state.currentUser.id;
			let enabled = true;

			this.selection.forEach(item => {
				const status = this.statusField ? item[this.statusField] : null;
				const permission = this.statusField
					? this.permission.statuses[status]
					: this.permission;
				const userID = item[this.userCreatedField] ? item[this.userCreatedField].id : null;

				if (permission.delete === 'none') {
					return (enabled = false);
				}

				if (permission.delete === 'mine' && userID !== currentUserID) {
					return (enabled = false);
				}

				if (permission.delete === 'role') {
					const userRole = this.$store.state.users[userID].role;
					const currentUserRole = this.$store.state.currentUser.role.id;

					if (userRole === currentUserRole) {
						enabled = true;
					}

					return;
				}
			});

			return enabled;
		},
		editButton() {
			if (this.selection && this.selection.length > 1) return true;
			return false;
		},
		editButtonEnabled() {
			const currentUserID = this.$store.state.currentUser.id;
			let enabled = true;

			if (this.$store.state.currentUser.admin) return true;

			this.selection.forEach(item => {
				const status = this.statusField ? item[this.statusField] : null;
				const permission = this.statusField
					? this.permission.statuses[status]
					: this.permission;
				const userID = item[this.userCreatedField] ? item[this.userCreatedField].id : null;

				if (permission.update === 'none') {
					return (enabled = false);
				}

				if (permission.update === 'mine' && userID !== currentUserID) {
					return (enabled = false);
				}

				if (permission.update === 'role') {
					const userRole = this.$store.state.users[userID].role;
					const currentUserRole = this.$store.state.currentUser.role.id;

					if (userRole === currentUserRole) {
						enabled = true;
						return;
					}
				}
			});

			return enabled;
		}
	},
	watch: {
		$route() {
			if (this.$route.query.b) {
				this.$router.replace({
					path: this.$route.path
				});
			}
		}
	},
	methods: {
		keyBy: keyBy,
		setMeta(meta) {
			this.meta = meta;
		},
		editCollection() {
			if (!this.$store.state.currentUser.admin) return;
			this.$router.push(`/${this.currentProjectKey}/settings/collections/${this.collection}`);
		},
		closeBookmark() {
			this.bookmarkModal = false;
		},
		setViewQuery(query) {
			const newViewQuery = {
				...this.preferences.view_query,
				[this.viewType]: {
					...this.viewQuery,
					...query
				}
			};
			this.updatePreferences('view_query', newViewQuery);
		},
		setViewOptions(options) {
			const newViewOptions = {
				...this.preferences.view_options,
				[this.viewType]: {
					...this.viewOptions,
					...options
				}
			};
			this.updatePreferences('view_options', newViewOptions);
		},
		updatePreferences(key, value, combine = false) {
			if (combine) {
				value = {
					...this.preferences[key],
					...value
				};
			}
			this.$set(this.preferences, key, value);

			// user vs role vs collection level preferences, == checks both null and undefined
			const isPreferenceFallback = this.preferences.user == null;
			if (isPreferenceFallback) {
				return this.createCollectionPreset();
			}

			const id = this.$helpers.shortid.generate();
			this.$store.dispatch('loadingStart', { id });

			return this.$api
				.updateCollectionPreset(this.preferences.id, {
					[key]: value
				})
				.then(() => {
					this.$store.dispatch('loadingFinished', id);
				})
				.catch(error => {
					this.$store.dispatch('loadingFinished', id);
					this.$events.emit('error', {
						notify: this.$t('something_went_wrong_body'),
						error
					});
				});
		},
		createCollectionPreset() {
			const id = this.$helpers.shortid.generate();
			this.$store.dispatch('loadingStart', { id });

			const preferences = { ...this.preferences };
			delete preferences.id;

			return this.$api
				.createCollectionPreset({
					...preferences,
					collection: this.collection,
					user: this.$store.state.currentUser.id
				})
				.then(({ data }) => {
					this.$store.dispatch('loadingFinished', id);
					this.$set(this.preferences, 'id', data.id);
					this.$set(this.preferences, 'user', data.user);
				})
				.catch(error => {
					this.$store.dispatch('loadingFinished', id);
					this.$events.emit('error', {
						notify: this.$t('something_went_wrong_body'),
						error
					});
				});
		},
		clearFilters() {
			this.updatePreferences('filters', null);
			this.updatePreferences('search_query', null);
		},
		remove() {
			const id = this.$helpers.shortid.generate();
			this.$store.dispatch('loadingStart', { id });

			let request;

			const itemKeys = this.selection.map(item => item[this.primaryKeyField]);

			if (this.softDeleteStatus) {
				request = this.$api.updateItem(this.collection, itemKeys.join(','), {
					[this.statusField]: this.softDeleteStatus
				});
			} else {
				request = this.$api.deleteItems(
					this.collection,
					this.selection.map(item => item[this.primaryKeyField])
				);
			}

			request
				.then(() => {
					this.$store.dispatch('loadingFinished', id);
					this.$refs.listing.getItems();
					this.selection = [];
					this.confirmRemove = false;
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
		let { collection } = to.params;

		if (to.path.endsWith('webhooks')) collection = 'directus_webhooks';

		const collectionInfo = store.state.collections[collection] || null;

		if (collection.startsWith('directus_') === false && collectionInfo === null) {
			return next(vm => (vm.notFound = true));
		}

		if (collectionInfo && collectionInfo.single) {
			return next(`/${store.state.currentProjectKey}/collections/${collection}/1`);
		}

		const id = shortid.generate();
		store.dispatch('loadingStart', { id });

		return api
			.getMyListingPreferences(collection)
			.then(preferences => {
				store.dispatch('loadingFinished', id);
				next(vm => {
					vm.$data.preferences = preferences;
				});
			})
			.catch(error => {
				store.dispatch('loadingFinished', id);
				this.$events.emit('error', {
					notify: this.$t('something_went_wrong_body'),
					error
				});
			});
	},
	beforeRouteUpdate(to, from, next) {
		const { collection } = to.params;

		this.preferences = null;
		this.selection = [];
		this.meta = {};
		this.notFound = false;

		const collectionInfo = this.$store.state.collections[collection] || null;

		if (collection.startsWith('directus_') === false && collectionInfo === null) {
			this.notFound = true;
			return next();
		}

		if (collectionInfo && collectionInfo.single) {
			return next(`/${this.$store.state.currentProjectKey}/collections/${collection}/1`);
		}

		const id = this.$helpers.shortid.generate();
		this.$store.dispatch('loadingStart', { id });

		return api
			.getMyListingPreferences(collection)
			.then(preferences => {
				this.$store.dispatch('loadingFinished', id);
				this.preferences = preferences;
				next();
			})
			.catch(error => {
				this.$store.dispatch('loadingFinished', id);
				this.$events.emit('error', {
					notify: this.$t('something_went_wrong_body'),
					error
				});
			});
	}
};
</script>

<style lang="scss" scoped>
.bookmark,
.settings {
	margin-left: 8px;
	position: relative;

	.v-icon {
		transition: color var(--fast) var(--transition);
		color: var(--input-border-color);
	}

	&:hover {
		i {
			color: var(--input-border-color-hover);
		}
	}
}

.layout-picker {
	color: var(--sidebar-text-color);
	background-color: var(--input-background-color);
	border: var(--input-border-width) solid var(--input-border-color);
	border-radius: var(--border-radius);
	height: var(--input-height);
	padding: 8px 4px;
	position: relative;
	display: block;

	.preview {
		display: flex;
		align-items: center;

		.label {
			margin-left: 8px;
			flex-grow: 1;
		}
	}

	select {
		opacity: 0;
		width: 100%;
		height: 100%;
		position: absolute;
		top: 0;
		left: 0;
		cursor: pointer;
		appearance: menulist-button;
	}
}

.layout-options {
	margin-bottom: 64px;
}
</style>
