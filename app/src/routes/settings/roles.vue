<template>
	<div class="settings-roles">
		<v-header :breadcrumb="breadcrumb" :icon-link="`/${currentProjectKey}/settings`" settings>
			<template slot="buttons">
				<v-header-button
					key="add"
					icon="add"
					background-color="button-primary-background-color"
					icon-color="button-primary-text-color"
					:label="$t('new')"
					@click="addNew = true"
				/>
			</template>
		</v-header>
		<v-table :items="items" :columns="fields" primary-key-field="collection" link="__link__" />

		<portal v-if="addNew" to="modal">
			<v-prompt
				v-model="newName"
				:confirm-text="$t('create')"
				:message="$t('create_role')"
				:placeholder="$t('enter_role_name')"
				:loading="adding"
				required
				@cancel="addNew = false"
				@confirm="add"
			/>
		</portal>
		<v-info-sidebar wide>
			<span class="type-note">No settings</span>
		</v-info-sidebar>
	</div>
</template>

<script>
import api from '../../api';
import { mapState } from 'vuex';

export default {
	name: 'SettingsRoles',
	metaInfo() {
		return {
			title: `${this.$t('settings')} | ${this.$t('roles')}`
		};
	},
	data() {
		return {
			error: null,
			roles: [],
			adding: false,
			addNew: false,
			newName: ''
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
					name: this.$t('roles'),
					path: `/${this.currentProjectKey}/settings/roles`
				}
			];
		},
		items() {
			return this.roles.map(role => ({
				...role,
				__link__: `/${this.currentProjectKey}/settings/roles/${role.id}`
			}));
		},
		fields() {
			return [
				{
					field: 'name',
					name: this.$t('name')
				},
				{
					field: 'description',
					name: this.$t('description')
				}
			];
		}
	},
	beforeRouteEnter(to, from, next) {
		api.getRoles()
			.then(res => res.data)
			.then(roles => {
				next(vm => {
					vm.$data.roles = roles;
				});
			})
			.catch(error => {
				this.$events.emit('error', {
					notify: this.$t('something_went_wrong_body'),
					error
				});
				next(vm => {
					vm.$data.error = error;
				});
			});
	},
	methods: {
		add() {
			this.adding = true;

			const id = this.$helpers.shortid.generate();
			this.$store.dispatch('loadingStart', { id });

			this.$api
				.createRole({
					name: this.newName
				})
				.then(res => res.data)
				.then(role => {
					this.$store.dispatch('loadingFinished', id);
					this.$router.push(`/${this.currentProjectKey}/settings/roles/${role.id}`);
				})
				.catch(error => {
					this.adding = false;
					this.$store.dispatch('loadingFinished', id);
					this.$events.emit('error', {
						notify: this.$t('something_went_wrong_body'),
						error
					});
				});
		}
	}
};
</script>

<style lang="scss" scoped>
.settings-roles {
	padding: var(--page-padding) var(--page-padding) var(--page-padding-bottom);
}
</style>
