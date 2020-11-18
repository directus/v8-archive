<template>
	<v-not-found v-if="notFound" />
	<div v-else class="route-file-library">
		<v-header info-toggle :breadcrumb="breadcrumb" icon="photo_library">
			<template slot="title">
				<button
					:class="currentBookmark ? 'active' : null"
					:disabled="currentBookmark"
					class="bookmark"
					@click="bookmarkModal = true"
				>
					<v-icon :name="currentBookmark ? 'bookmark' : 'bookmark_border'" />
				</button>
				<div v-if="currentBookmark" class="bookmark-name no-wrap">
					({{ currentBookmark.title }})
				</div>
			</template>
			<v-search-filter
				v-show="selection.length === 0 && !emptyCollection"
				:filters="filters"
				:search-query="searchQuery"
				:field-names="filterableFieldNames"
				collection-name="directus_files"
				:placeholder="resultCopy"
				@filter="updatePreferences('filters', $event)"
				@search="updatePreferences('search_query', $event)"
				@clear-filters="clearFilters"
			/>
			<template slot="buttons">
				<v-header-button
					v-if="selection.length > 1"
					key="edit"
					icon="mode_edit"
					background-color="warning"
					icon-color="white"
					hover-color="warning-dark"
					:label="$t('batch')"
					:to="batchURL"
				/>
				<v-header-button
					v-if="selection.length"
					key="delete"
					icon="delete_outline"
					background-color="danger"
					icon-color="white"
					hover-color="danger-dark"
					:label="$t('delete')"
					@click="confirmRemove = true"
				/>
				<v-header-button
					key="add"
					icon="add"
					background-color="button-primary-background-color"
					icon-color="button-primary-text-color"
					:label="$t('new')"
					@click="newModal = true"
				/>
			</template>
		</v-header>

		<v-items
			v-if="preferences"
			ref="listing"
			:key="key"
			:collection="collection"
			:filters="filters"
			:search-query="searchQuery"
			:view-query="viewQuery"
			:view-type="viewType"
			:view-options="viewOptions"
			:selection="selection"
			links
			@fetch="meta = $event"
			@options="setViewOptions"
			@select="selection = $event"
			@query="setViewQuery"
		/>

		<v-info-sidebar v-if="preferences">
			<template slot="system">
				<label for="listing" class="type-label">{{ $t('view_type') }}</label>
				<v-select
					id="listing"
					:options="layoutNames"
					:value="viewType"
					name="listing"
					@input="updatePreferences('view_type', $event)"
				/>
			</template>
			<v-ext-layout-options
				:key="`${collection}-${viewType}`"
				:type="viewType"
				:collection="collection"
				:fields="keyBy(fields, 'field')"
				:view-options="viewOptions"
				:view-query="viewQuery"
				:selection="selection"
				primary-key-field="id"
				link="__link__"
				@query="setViewQuery"
				@options="setViewOptions"
			/>
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
			<v-prompt
				v-model="bookmarkTitle"
				:message="$t('name_bookmark')"
				@cancel="cancelBookmark"
				@confirm="saveBookmark"
			/>
		</portal>

		<portal v-if="newModal" to="modal">
			<v-modal
				:title="$t('file_upload')"
				:buttons="{
					done: {
						text: $t('done')
					}
				}"
				@done="newModal = false"
				@close="newModal = false"
			>
				<div class="modal-body">
					<v-upload @upload="key = $helpers.shortid.generate()" />
				</div>
			</v-modal>
		</portal>
	</div>
</template>

<script>
import shortid from 'shortid';
import store from '@/store/';
import VSearchFilter from '../components/search-filter/search-filter.vue';
import VNotFound from './not-found.vue';
import { mapState } from 'vuex';
import { isEqual, isEmpty, isNil, keyBy } from 'lodash';

import api from '../api';

export default {
	name: 'RouteFileLibrary',
	metaInfo() {
		return {
			title: this.$t('file_library')
		};
	},
	components: {
		VSearchFilter,
		VNotFound
	},
	data() {
		return {
			selection: [],
			meta: null,
			preferences: null,
			confirmRemove: false,

			bookmarkModal: false,
			bookmarkTitle: '',

			notFound: false,

			newModal: false,

			// Changing the key makes the items refresh & reload
			key: 'init'
		};
	},
	computed: {
		...mapState(['currentProjectKey']),
		breadcrumb() {
			return [
				{
					name: this.$t('file_library'),
					path: `/${this.currentProjectKey}/files`
				}
			];
		},
		batchURL() {
			return `/${this.currentProjectKey}/files/${this.selection
				.map(item => item.id)
				.join(',')}`;
		},
		fields() {
			const fields = this.$store.state.collections[this.collection].fields;
			return Object.values(fields).map(field => ({
				...field,
				name: this.$helpers.formatField(field.field, field.collection)
			}));
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
			return 'directus_files';
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
			return (
				(this.preferences.view_query && this.preferences.view_query[this.viewType]) || {}
			);
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

			return isFiltering
				? this.$tc('item_count_filter', this.meta.result_count, {
						count: this.$n(this.meta.result_count)
				  })
				: this.$tc('item_count', this.meta.total_count, {
						count: this.$n(this.meta.total_count)
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
		cancelBookmark() {
			this.bookmarkTitle = '';
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

			this.$api
				.deleteItems(
					this.collection,
					this.selection.map(item => item.id)
				)
				.then(() => {
					this.$store.dispatch('loadingFinished', id);
					this.$refs.listing.getItems();
					this.selection = [];
				})
				.catch(error => {
					this.$store.dispatch('loadingFinished', id);
					this.$events.emit('error', {
						notify: this.$t('something_went_wrong_body'),
						error
					});
				});
			this.confirmRemove = false;
		},
		saveBookmark() {
			const preferences = { ...this.preferences };
			preferences.user = this.$store.state.currentUser.id;
			preferences.title = this.bookmarkTitle;
			delete preferences.id;
			delete preferences.role;
			if (!preferences.collection) {
				preferences.collection = this.collection;
			}
			const id = this.$helpers.shortid.generate();
			this.$store.dispatch('loadingStart', { id });

			this.$store
				.dispatch('saveBookmark', preferences)
				.then(() => {
					this.$store.dispatch('loadingFinished', id);
					this.bookmarkModal = false;
					this.bookmarkTitle = '';
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
		const collection = 'directus_files';

		const collectionInfo = store.state.collections[collection] || null;

		if (collection.startsWith('directus_') === false && collectionInfo === null) {
			return next(vm => (vm.notFound = true));
		}

		if (collectionInfo && collectionInfo.single) {
			return next(`/${store.state.currentProjectKey}/collections/${collection}/1`);
		}

		const id = shortid.generate();
		store.dispatch('loadingStart', { id });

		return Promise.all([api.getMyListingPreferences(collection)])
			.then(([preferences]) => ({
				preferences
			}))
			.then(({ preferences }) => {
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
		const collection = 'directus_files';

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
			return next(`/${store.state.currentProjectKey}/collections/${collection}/1`);
		}

		const id = this.$helpers.shortid.generate();
		this.$store.dispatch('loadingStart', { id });

		return Promise.all([api.getMyListingPreferences(collection)])
			.then(([preferences]) => ({
				preferences
			}))
			.then(({ preferences }) => {
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
.type-label {
	padding-bottom: var(--input-label-margin);
}
.bookmark {
	margin-left: 8px;
	transition: opacity var(--fast) var(--transition);
	color: var(--input-border-color);
	position: relative;
	&:hover {
		color: var(--input-border-color-hover);
	}
	i {
		font-size: 24px;
		height: 20px;
		transform: translateY(-1px); // Vertical alignment of icon
	}
}
.bookmark.active {
	opacity: 1;
	i {
		color: var(--input-background-color-active);
	}
}
.bookmark-name {
	color: var(--blue-grey-900);
	margin-left: 5px;
	margin-top: 3px;
	font-size: 0.77em;
	line-height: 1.1;
	font-weight: 700;
	text-transform: uppercase;
}

.modal-body {
	padding: 20px;
}
</style>
