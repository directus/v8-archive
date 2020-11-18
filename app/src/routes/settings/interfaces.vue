<template>
	<div class="interfaces">
		<v-header :breadcrumb="links" :icon-link="`/${currentProjectKey}/settings`" settings />
		<v-table :columns="columns" :items="items" link="__link" primary-key-field="id" />
		<v-info-sidebar wide>
			<span class="type-note">No settings</span>
		</v-info-sidebar>
	</div>
</template>

<script>
import { mapState } from 'vuex';

export default {
	metaInfo() {
		return {
			title: 'Interfaces'
		};
	},
	computed: {
		...mapState(['currentProjectKey']),
		links() {
			return [
				{
					name: this.$t('settings'),
					path: `/${this.currentProjectKey}/settings`
				},
				{
					name: this.$tc('interface', 2),
					path: `/${this.currentProjectKey}/settings/interfaces`
				}
			];
		},
		items() {
			return Object.keys(this.$store.state.extensions.interfaces).map(id => ({
				...this.$store.state.extensions.interfaces[id],
				__link: `/${this.currentProjectKey}/settings/interfaces/${id}`
			}));
		},
		columns() {
			return [
				{
					field: 'name',
					name: 'Name'
				},
				{
					field: 'id',
					name: 'ID'
				},
				{
					field: 'version',
					name: 'Version'
				}
			];
		}
	}
};
</script>

<style lang="scss" scoped>
.interfaces {
	padding: 0 32px var(--page-padding-bottom);
}
</style>
