<template>
	<div class="layout-cards" @scroll="onScroll">
		<div class="cards" :class="{ loading: loading }">
			<v-card
				v-for="(item, index) in items"
				:key="item.id"
				:to="item[link]"
				:icon="emptySrc(item) ? viewOptions.icon || 'photo' : null"
				:opacity="emptySrc(item) ? 'half' : null"
				:src="src(item)"
				:selected="selection.includes(item.id)"
				:selection-mode="selection.length > 0"
				@select="select(item.id)"
			>
				<template slot="title">
					<v-ext-display
						:key="`card-title-${fields[title].interface}-${index}`"
						:interface-type="fields[title].interface"
						:name="title"
						:collection="collection"
						:type="fields[title].type"
						:options="fields[title].options"
						:value="item[title]"
						:values="item"
						:relation="fields[title].relation"
					/>
				</template>
				<template v-if="subtitle" slot="subtitle">
					<v-ext-display
						:key="`card-subtitle-${fields[subtitle].interface}-${index}`"
						:interface-type="fields[subtitle].interface"
						:name="subtitle"
						:collection="collection"
						:type="fields[subtitle].type"
						:options="fields[subtitle].options"
						:value="item[subtitle]"
						:values="item"
						:relation="fields[subtitle].relation"
					/>
				</template>
			</v-card>
			<v-card
				v-if="lazyLoading"
				icon="hourglass_empty"
				opacity="half"
				:title="$t('loading_more')"
				:subtitle="$t('one_moment')"
			></v-card>
		</div>

		<div v-if="loading" class="layout-loading">
			<v-spinner />
		</div>
	</div>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/layout';
import { mapState } from 'vuex';

export default {
	name: 'LayoutCards',
	mixins: [mixin],
	computed: {
		...mapState(['currentProjectKey']),
		title() {
			return this.viewOptions.title || this.primaryKeyField;
		},
		subtitle() {
			return this.viewOptions.subtitle;
		},
		content() {
			return this.viewOptions.content;
		}
	},
	methods: {
		src(item) {
			const srcField = this.viewOptions.src || null;

			if (srcField) {
				let file = item[srcField];

				if (srcField === 'data' && this.collection === 'directus_files') {
					file = item;
				}

				if (!file) return null;

				if (file.type.startsWith('image') === false) return null;

				if (file.type.includes('svg')) return file.data.asset_url;

				const fit = this.viewOptions.fit || 'crop';

				return file.data.thumbnails.find(thumb => thumb.key === `directus-medium-${fit}`)
					?.url;
			}

			return null;
		},
		emptySrc(item) {
			return this.viewOptions.src != null && this.src(item) === null;
		},
		onScroll(event) {
			const { scrollHeight, clientHeight, scrollTop } = event.srcElement;
			const totalScroll = scrollHeight - clientHeight;
			const delta = totalScroll - scrollTop;
			if (delta <= 500) this.$emit('next-page');
			this.scrolled = scrollTop > 0;
		},
		select(id) {
			let newSelection;

			if (this.selection.includes(id)) {
				newSelection = this.selection.filter(selectedID => selectedID !== id);
			} else {
				newSelection = [...this.selection, id];
			}

			this.$emit('select', newSelection);
		}
	}
};
</script>

<style lang="scss" scoped>
.layout-cards {
	overflow: auto;
	height: 100%;
	max-height: calc(100vh - var(--header-height-expanded));
}

.toolbar {
	background-color: var(--page-background-color);
	width: 100%;
	position: sticky;
	top: 0;
	z-index: +1;
	height: var(--header-height);
	padding: 28px var(--page-padding);
	display: flex;
	align-items: center;
	justify-content: flex-end;
	border-bottom: 2px solid var(--sidebar-background-color);
}

.sort-select {
	position: relative;
	display: flex;
	align-items: center;

	margin: 0 10px;

	&:last-of-type {
		margin-right: 0;
	}

	select {
		-webkit-appearance: none;
		-moz-appearance: none;
		appearance: none;
		vertical-align: middle;
		background-color: var(--input-background-color-alt);
		border-radius: var(--border-radius);
		border: 0;
		overflow: hidden;
		padding: 5px;
		padding-right: 15px;
		cursor: pointer;
		outline: 0;
	}

	.icon {
		pointer-events: none;
		position: absolute;
		right: 0;
		top: 50%;
		transform: translateY(-50%);
	}
}

.cards {
	padding: var(--page-padding-top) var(--page-padding) var(--page-padding-bottom);
	display: grid;
	grid-template-columns: repeat(auto-fill, var(--card-size));
	grid-gap: var(--card-vertical-gap) var(--card-horizontal-gap);
	justify-content: space-between;
	width: 100%;

	&.loading {
		opacity: 0.5;
	}
}

.layout-loading {
	padding: 24px 0;
	text-align: center;

	.v-spinner {
		display: inline-block;
	}
}
</style>
