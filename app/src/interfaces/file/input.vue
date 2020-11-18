<template>
	<div class="input-single-file">
		<v-notice v-if="noFileAccess" class="notice">
			{{ $t('this_item_is_not_available') }}
			<button @click="$emit('input', null)">
				<v-icon v-tooltip="$t('deselect')" name="clear" />
			</button>
		</v-notice>

		<v-spinner v-else-if="image === null && value !== null" />

		<template v-else>
			<v-card
				v-if="Array.isArray(value) === false && value"
				class="card"
				:title="image.title"
				:subtitle="subtitle + subtitleExtra"
				:src="src"
				:icon="icon"
				text-background
				color="black"
				:options="cardOptions"
				:medium-image="width.startsWith('half')"
				:big-image="width === 'full'"
				:only-show-on-hover="isImage"
				@download="downloadFile"
				@deselect="$emit('input', null)"
				@remove="removeFile"
			></v-card>
			<v-upload
				v-else
				small
				:disabled="readonly"
				class="uploader"
				:accept="options.accept"
				:multiple="false"
				@upload="saveUpload"
			></v-upload>

			<div v-if="!value" class="buttons">
				<v-button type="button" :disabled="readonly" @click="existing = true">
					<v-icon name="playlist_add" left />
					{{ $t('existing') }}
				</v-button>
			</div>

			<portal v-if="existing" to="modal">
				<v-modal
					:title="$t('choose_one')"
					:buttons="{
						done: {
							text: $t('done')
						}
					}"
					@cancel="existing = false"
					@close="existing = false"
					@done="existing = false"
				>
					<div class="content">
						<div class="search">
							<v-input
								type="search"
								:placeholder="$t('search_for_item')"
								class="search-input"
								@input="onSearchInput"
							/>
						</div>
						<v-items
							class="items"
							collection="directus_files"
							:view-type="viewType"
							:selection="value ? [value] : []"
							:filters="filters"
							:view-query="viewQuery"
							:view-options="viewOptions"
							@options="setViewOptions"
							@query="setViewQuery"
							@select="saveSelection"
						></v-items>
					</div>
				</v-modal>
			</portal>
		</template>
	</div>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';
import formatSize from '../file-size/format-size';
import getIcon from './get-icon';
import { mapState } from 'vuex';
import { debounce } from 'lodash';

export default {
	mixins: [mixin],
	data() {
		return {
			existing: false,
			viewOptionsOverride: {},
			viewTypeOverride: null,
			viewQueryOverride: {},
			filtersOverride: [],
			image: null,
			noFileAccess: false
		};
	},
	computed: {
		...mapState(['currentProjectKey']),
		cardOptions() {
			const options = {
				download: {
					text: this.$t('file_download'),
					icon: 'file_download'
				},
				deselect: {
					text: this.$t('deselect'),
					icon: 'clear'
				}
			};

			if (this.options.allowDelete === true) {
				options.remove = {
					text: this.$t('delete'),
					icon: 'delete'
				};
			}

			return options;
		},
		subtitle() {
			if (!this.image) return '';

			return (
				this.image.filename_disk
					.split('.')
					.pop()
					.toUpperCase() +
				' • ' +
				this.$d(new Date(this.image.uploaded_on.replace(/-/g, '/')), 'short')
			);
		},
		subtitleExtra() {
			// Image ? -> display dimensions and formatted filesize
			return this.image.type && this.image.type.startsWith('image')
				? ' • ' + formatSize(this.image.filesize)
				: '';
		},
		src() {
			if (!this.image.type || !this.image.type.startsWith('image')) {
				return null;
			}

			if (this.image.type === 'image/svg+xml') {
				return this.image.data.asset_url;
			}

			const size = this.width === 'full' ? 'large' : 'medium';
			const fit = this.options.crop ? 'crop' : 'contain';

			return this.image.data?.thumbnails.find(
				thumb => thumb.key === `directus-${size}-${fit}`
			)?.url;
		},
		isImage() {
			return this.image.type && this.image.type.startsWith('image');
		},
		icon() {
			return this.image.type && !this.image.type.startsWith('image')
				? getIcon(this.image.type)
				: null;
		},
		viewOptions() {
			const viewOptions = this.options.viewOptions;
			return {
				...viewOptions,
				...this.viewOptionsOverride
			};
		},
		viewType() {
			if (this.viewTypeOverride) return this.viewTypeOverride;
			return this.options.viewType;
		},
		viewQuery() {
			const viewQuery = this.options.viewQuery;

			return {
				sort: '-id',
				...viewQuery,
				...this.viewQueryOverride
			};
		},
		filters() {
			return [
				...(this.options.filters || []),
				...this.fileTypeFilters,
				...this.filtersOverride
			];
		},
		fileTypeFilters() {
			if (
				!this.options.accept ||
				this.filtersOverride.length > 0 ||
				(this.options.filters || []).some(filter => filter.field === 'type')
			) {
				return [];
			}

			return [
				{
					field: 'type',
					operator: 'in',
					value: (this.options.accept || '').trim().split(/,\s*/)
				}
			];
		}
	},
	async created() {
		if (this.value) {
			await this.fetchImage();
		}
		this.onSearchInput = debounce(this.onSearchInput, 200);
	},
	watch: {
		value() {
			this.fetchImage();
		}
	},
	methods: {
		async fetchImage() {
			this.noFileAccess = false;
			this.image = null;

			if (!this.value) return;

			let id;

			if (typeof this.value === 'object') {
				id = this.value.id;
			} else {
				id = this.value;
			}

			try {
				const response = await this.$api.getFile(String(id));
				this.image = response.data;
			} catch {
				this.noFileAccess = true;
			}
		},
		downloadFile() {
			window.open(this.image.data.full_url);
		},
		saveUpload(response) {
			this.image = response.data;
			// We know that the primary key of directus_files is called `id`
			this.$emit('input', this.image.id);
		},
		setViewOptions(updates) {
			this.viewOptionsOverride = {
				...this.viewOptionsOverride,
				...updates
			};
		},
		setViewQuery(updates) {
			this.viewQueryOverride = {
				...this.viewQueryOverride,
				...updates
			};
		},
		onSearchInput(value) {
			this.setViewQuery({
				q: value
			});
		},
		saveSelection(value) {
			const file = value[value.length - 1];

			if (file) {
				this.image = file;
				this.$emit('input', file);
			} else {
				this.image = null;
				this.$emit('input', null);
			}

			this.existing = false;
		},
		async removeFile() {
			const file = this.image;
			await this.$api.deleteItem('directus_files', file.id);
			this.$notify({
				title: this.$t('item_deleted'),
				color: 'green',
				iconMain: 'check'
			});
			this.image = null;
			this.$emit('input', null);
		}
	}
};
</script>

<style lang="scss" scoped>
.card,
.uploader {
	width: 100%;
	max-width: var(--width-x-large);
}

.uploader {
	height: 236px;
}

.buttons {
	margin-top: 24px;
}

button {
	display: inline-block;
	margin-left: 20px;
	&:first-of-type {
		margin-left: 0;
	}
}

.body {
	padding: 20px;
}

.search-input {
	border-bottom: 2px solid var(--input-border-color);
	&::v-deep input {
		border-radius: 0;
		border: none;
		padding-left: var(--page-padding);
		height: var(--header-height);

		&::placeholder {
			color: var(--input-placeholder-color);
		}
	}
}

.content {
	&::v-deep .v-layout {
		height: auto;
		max-height: none;
		overflow: hidden;
	}
}

.notice {
	display: flex;
	justify-content: space-between;
}
</style>
