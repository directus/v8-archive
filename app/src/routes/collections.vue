<template>
	<div class="collections">
		<v-header
			:breadcrumb="[
				{
					name: $tc('collection', 2),
					path: `/${currentProjectKey}/collections`
				}
			]"
			icon="box"
		/>
		<v-error
			v-if="items.length === 0"
			:title="$t('no_collections')"
			:body="$t('no_collections_body')"
			icon="error_outline"
		/>
		<div v-else class="padding">
			<v-table
				:items="items"
				:columns="fields"
				primary-key-field="collection"
				link="__link__"
				@select="select"
			/>
		</div>
		<v-info-sidebar wide>
			<span class="type-note">No settings</span>
		</v-info-sidebar>
	</div>
</template>

<script>
import VError from '../components/error.vue';
import { mapState } from 'vuex';
import { some } from 'lodash';

export default {
	name: 'Collections',
	metaInfo() {
		return {
			title: this.$tc('collection', 2)
		};
	},
	components: {
		VError
	},
	computed: {
		...mapState(['currentProjectKey']),
		items() {
			if (this.collections == null) return [];

			return Object.values(this.collections)
				.filter(
					collection =>
						collection.hidden == false &&
						collection.managed == true &&
						collection.collection.startsWith('directus_') === false
				)
				.filter(collection => {
					if (collection.status_mapping) {
						return some(
							this.permissions[collection.collection].statuses,
							permission => permission.read !== 'none'
						);
					}

					return this.permissions[collection.collection].read !== 'none';
				})
				.map(collection => ({
					...collection,
					collection: this.$helpers.formatCollection(collection.collection),
					__link__: `/${this.currentProjectKey}/collections/${collection.collection}`
				}));
		},
		fields() {
			return [
				{
					field: 'collection',
					name: this.$tc('collection', 1)
				},
				{
					field: 'note',
					name: this.$t('note')
				}
			];
		},
		collections() {
			return this.$store.state.collections;
		},
		permissions() {
			return this.$store.state.permissions;
		}
	},
	methods: {
		select(selection) {
			this.selection = selection;
		}
	}
};
</script>

<style lang="scss" scoped>
.collections {
	padding: var(--page-padding-top-table) var(--page-padding) var(--page-padding-bottom);
}
</style>
