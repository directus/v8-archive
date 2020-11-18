<template>
	<div class="nav-sidebar">
		<v-blocker v-show="active" class="blocker" :z-index="2" @click="disableNav" />
		<transition name="nav">
			<aside :class="{ active }">
				<button class="a11y-close" @click="disableNav">Close nav</button>

				<module-bar />

				<section class="main-bar">
					<project-switcher />

					<nav-menu
						v-if="customCollections === null"
						class="menu-section"
						:links="defaultCollections"
					/>

					<template v-else>
						<nav-menu
							v-for="(group, index) in customCollections"
							:key="index"
							:title="group.title"
							:links="group.links"
							class="menu-section"
						/>
					</template>

					<nav-bookmarks
						v-if="bookmarks && bookmarks.length > 0"
						class="menu-section"
						:bookmarks="bookmarks"
					/>
				</section>
			</aside>
		</transition>
	</div>
</template>
<script>
import ProjectSwitcher from './project-switcher.vue';
import NavMenu from './nav-menu.vue';
import NavBookmarks from './nav-bookmarks.vue';
import VBlocker from '../../blocker.vue';
import { TOGGLE_NAV } from '@/store/mutation-types';
import { mapState } from 'vuex';
import ModuleBar from './module-bar';
import { some } from 'lodash';

export default {
	name: 'NavSidebar',
	components: {
		ProjectSwitcher,
		NavMenu,
		NavBookmarks,
		VBlocker,
		ModuleBar
	},
	computed: {
		...mapState(['currentProjectKey', 'currentUser']),
		permissions() {
			return this.$store.state.permissions;
		},
		collections() {
			const collections = this.$store.state.collections;

			if (collections == null) return [];

			return Object.values(collections)
				.filter(
					collection =>
						collection.hidden == false &&
						collection.managed == true &&
						collection.collection.startsWith('directus_') === false
				)
				.filter(collection => {
					if (
						collection.status_mapping &&
						this.permissions[collection.collection].statuses
					) {
						return some(
							this.permissions[collection.collection].statuses,
							permission => permission.read !== 'none'
						);
					}

					return this.permissions[collection.collection].read !== 'none';
				});
		},
		projectName() {
			return this.$store.getters.currentProject.project_name;
		},
		active() {
			return this.$store.state.sidebars.nav;
		},
		bookmarks() {
			return this.$store.state.bookmarks;
		},
		customCollections() {
			const collectionListing = this.currentUser.role.collection_listing;
			const hasCustom = Array.isArray(collectionListing) && collectionListing.length > 0;

			if (hasCustom === false) return null;

			return collectionListing.map(group => {
				return {
					title: group.group_name,
					links: (group.collections || []).map(({ collection }) => {
						const collectionInfo = this.collections.find(
							c => c.collection === collection
						);

						return {
							link: `/${this.currentProjectKey}/collections/${collection}`,
							name: this.$helpers.formatCollection(collection),
							icon: collectionInfo ? collectionInfo.icon : null
						};
					})
				};
			});
		},
		defaultCollections() {
			return this.collections
				.map(({ collection, icon }) => ({
					link: `/${this.currentProjectKey}/collections/${collection}`,
					name: this.$helpers.formatCollection(collection),
					icon
				}))
				.sort((a, b) => (a.name > b.name ? 1 : -1));
		}
	},
	methods: {
		logout() {
			this.$store.dispatch('logout');
		},
		deleteBookmark(id) {
			this.$store.dispatch('deleteBookmark', id);
		},
		toBookmark(bookmark) {
			/* eslint-disable camelcase */
			const {
				collection,
				search_query,
				filters,
				view_options,
				view_type,
				view_query
			} = bookmark;

			this.$store
				.dispatch('setListingPreferences', {
					collection,
					updates: {
						search_query,
						filters,
						view_options,
						view_type,
						view_query
					}
				})
				.then(() => {
					this.$router.push(`/${this.currentProjectKey}/collections/${collection}`);
				});
		},
		disableNav() {
			this.$store.commit(TOGGLE_NAV, false);
		}
	}
};
</script>

<style lang="scss" scoped>
aside {
	position: fixed;
	top: 0;
	left: 0;
	height: 100%;
	z-index: 30;
	width: var(--nav-sidebar-width);
	background-color: var(--sidebar-background-color);
	color: var(--sidebar-text-color);
	display: flex;

	transform: translateX(-100%);
	visibility: hidden;
	transition: transform var(--slow) var(--transition-out),
		visibility 0ms var(--transition-out) var(--slow);

	&.active {
		transform: translateX(0);
		transition: transform var(--slow) var(--transition-in);
		visibility: visible;
	}

	@media (min-width: 800px) {
		transform: translateX(0);
		transition: none;
		visibility: visible;
		width: var(--nav-sidebar-width);
	}

	> div {
		height: 100%;
	}

	& .a11y-close {
		position: absolute;
		z-index: 15;
		left: -999px;
		background-color: yellow;
		padding: 5px;

		.user-is-tabbing &:focus {
			top: 13px;
			left: 13px;
		}

		@media (min-width: 800px) {
			display: none;
		}
	}
}

.main-bar {
	position: relative;
	padding: 12px;
	padding-top: 0;
	height: 100%;
	overflow: auto;
	-webkit-overflow-scrolling: touch;
	flex-basis: 220px;
	flex-shrink: 0;
}

.menu-section + .menu-section {
	border-top: 2px solid var(--sidebar-background-color-alt);
	padding-top: 16px;
}
</style>

<style>
@media (min-width: 800px) {
	body {
		padding-left: var(--nav-sidebar-width);
	}
}
</style>
