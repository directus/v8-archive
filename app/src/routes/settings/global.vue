<template>
	<div class="settings-global">
		<v-header :breadcrumb="links" :icon-link="`/${currentProjectKey}/settings`" settings>
			<template slot="buttons">
				<v-header-button
					:disabled="!editing"
					:loading="saving"
					:label="$t('save')"
					icon="check"
					background-color="button-primary-background-color"
					icon-color="button-primary-text-color"
					@click="save('leave')"
				/>
			</template>
		</v-header>

		<v-form
			:fields="fields"
			:values="values"
			collection="directus_settings"
			@stage-value="stageValue"
		/>
		<v-info-sidebar wide>
			<span class="type-note">No settings</span>
		</v-info-sidebar>
	</div>
</template>

<script>
import { mapState } from 'vuex';

export default {
	name: 'SettingsGlobal',
	metaInfo() {
		return {
			title: `${this.$t('settings')} | ${this.$t('settings_global')}`
		};
	},
	data() {
		return {
			saving: false,
			edits: {}
		};
	},
	computed: {
		...mapState({
			settings: state => state.settings.values,
			fields: state => state.collections.directus_settings.fields,
			currentProjectKey: state => state.currentProjectKey
		}),
		values() {
			return {
				...this.settings,
				...this.edits
			};
		},
		links() {
			return [
				{
					name: this.$t('settings'),
					path: `/${this.currentProjectKey}/settings`
				},
				{
					name: this.$t('settings_global'),
					path: `/${this.currentProjectKey}/settings/global`
				}
			];
		},
		editing() {
			return Object.keys(this.edits).length > 0;
		}
	},
	methods: {
		stageValue({ field, value }) {
			if (this.settings[field] == value) {
				return this.$delete(this.edits, field);
			}

			return this.$set(this.edits, field, value);
		},
		save() {
			this.saving = true;

			this.$store
				.dispatch('setSettings', this.edits)
				.then(() => {
					this.saving = false;
					this.edits = {};

					// Update the current project's info in the store when saving settings, this makes sure
					// the logo / name in the top left etc will update
					this.$store.dispatch('updateProjectInfo', this.currentProjectKey);

					this.$router.push(`/${this.currentProjectKey}/settings`);
					this.$notify({
						title: this.$t('settings_saved'),
						color: 'green',
						iconMain: 'check'
					});
				})
				.catch(error => {
					this.saving = false;
					this.$events.emit('error', {
						notify: error.message || this.$t('something_went_wrong_body'),
						error
					});
				});
		}
	}
};
</script>

<style lang="scss" scoped>
.settings-global {
	padding: var(--page-padding-top) var(--page-padding) var(--page-padding-bottom);
}
</style>
