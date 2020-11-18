<template>
	<div class="settings">
		<v-header :breadcrumb="links" icon="settings" settings />

		<v-details :title="$t('settings_project')" type="break" open>
			<nav>
				<ul>
					<v-card
						:title="$t('settings_global')"
						:subtitle="$tc('item_count', globalNum, { count: globalNum })"
						element="li"
						:to="`/${currentProjectKey}/settings/global`"
						icon="public"
					/>

					<v-card
						:title="$t('settings_collections_fields')"
						:subtitle="
							$tc('collection_count', collectionsNum, { count: collectionsNum })
						"
						element="li"
						:to="`/${currentProjectKey}/settings/collections`"
						icon="box"
					/>

					<v-card
						:title="$t('settings_permissions')"
						:subtitle="roleCount"
						element="li"
						:to="`/${currentProjectKey}/settings/roles`"
						icon="group"
					/>

					<v-card
						:title="$t('settings_webhooks')"
						:subtitle="webhookCount"
						element="li"
						:to="`/${currentProjectKey}/settings/webhooks`"
						icon="send"
					/>
				</ul>
			</nav>
		</v-details>

		<v-details :title="$t('additional_info')" type="break" open>
			<nav>
				<ul>
					<v-card
						:title="$tc('interface', 2)"
						:subtitle="
							$tc('interface_count', interfaceCount, { count: interfaceCount })
						"
						element="li"
						:to="`/${currentProjectKey}/settings/interfaces`"
						icon="extension"
					/>

					<v-card
						:title="$t('activity_log')"
						:subtitle="activityCount"
						element="li"
						:to="`/${currentProjectKey}/activity`"
						icon="assignment"
					/>

					<v-card
						:title="$t('about_directus')"
						:subtitle="$t('learn_more')"
						element="li"
						href="https://directus.io"
						icon="info_outline"
					/>

					<v-card
						:title="$t('report_issue')"
						:subtitle="$t('open_on_gh')"
						element="li"
						href="https://github.com/directus/app/issues/new?template=Bug_report.md"
						icon="bug_report"
					/>

					<v-card
						:title="$t('request_feature')"
						:subtitle="$t('open_on_gh')"
						element="li"
						href="https://github.com/directus/app/issues/new?template=Feature_request.md"
						icon="how_to_vote"
					/>
				</ul>
			</nav>
		</v-details>
		<v-details :title="$t('coming_soon')" type="break" open>
			<nav>
				<ul>
					<v-card
						:title="$t('connection')"
						:subtitle="
							`${$t('latency')}: ${$n(
								Math.round(
									$store.state.latency[$store.state.latency.length - 1].latency
								)
							)}ms`
						"
						disabled
						element="li"
					>
						<v-signal slot="icon" class="signal" />
					</v-card>

					<v-card
						:title="$t('server_details')"
						disabled
						:subtitle="projectName"
						element="li"
						icon="storage"
					/>

					<v-card
						:title="$t('version_and_updates')"
						disabled
						:subtitle="version"
						element="li"
						icon="update"
					/>
				</ul>
			</nav>
		</v-details>

		<v-info-sidebar wide>
			<span class="type-note">No settings</span>
		</v-info-sidebar>
	</div>
</template>

<script>
import { version } from '../../../package.json';
import VSignal from '../../components/signal.vue';
import { mapGetters, mapState } from 'vuex';

export default {
	name: 'Settings',
	metaInfo() {
		return {
			title: `${this.$t('settings')}`
		};
	},
	components: {
		VSignal
	},
	data() {
		return {
			roleCount: this.$t('loading'),
			activityCount: this.$t('loading'),
			webhookCount: this.$t('loading')
		};
	},
	computed: {
		...mapGetters(['currentProject']),
		...mapState(['currentProjectKey']),
		globalNum() {
			return Object.keys(this.$store.state.collections.directus_settings.fields).length;
		},
		collectionsNum() {
			return Object.keys(this.$store.state.collections).filter(
				name => name.startsWith('directus_') === false
			).length;
		},
		projectName() {
			return this.currentProject.project_name;
		},
		interfaceCount() {
			return Object.keys(this.$store.state.extensions.interfaces).length;
		},
		version() {
			return version;
		},
		links() {
			return [
				{
					name: this.$t('settings'),
					path: `/${this.currentProjectKey}/settings`
				}
			];
		}
	},
	created() {
		this.getRoleCount();
		this.getActivityCount();
		this.getWebhookCount();
	},
	methods: {
		getRoleCount() {
			this.$api
				.getItems('directus_roles', {
					fields: '-',
					limit: 0,
					meta: 'total_count'
				})
				.then(res => res.meta)
				.then(({ total_count }) => {
					this.roleCount = this.$tc('role_count', total_count, {
						count: this.$n(total_count)
					});
				})
				.catch(() => {
					this.roleCount = '--';
				});
		},
		getActivityCount() {
			this.$api
				.getItems('directus_activity', {
					fields: '-',
					limit: 0,
					meta: 'total_count'
				})
				.then(res => res.meta)
				.then(({ total_count }) => {
					this.activityCount = this.$tc('event_count', total_count, {
						count: this.$n(total_count)
					});
				})
				.catch(() => {
					this.activityCount = '--';
				});
		},
		getWebhookCount() {
			this.$api
				.getItems('directus_webhooks', {
					limit: 0,
					meta: 'total_count'
				})
				.then(res => res.meta)
				.then(({ total_count }) => {
					this.webhookCount = this.$tc('webhook_count', total_count, {
						count: this.$n(total_count)
					});
				})
				.catch(() => {
					this.webhookCount = '--';
				});
		}
	}
};
</script>

<style lang="scss" scoped>
.settings {
	padding: var(--page-padding-top) var(--page-padding) var(--page-padding-bottom);
}

nav ul {
	padding: 0;
	display: grid;
	grid-template-columns: repeat(auto-fill, var(--card-size));
	grid-gap: var(--card-horizontal-gap);

	li {
		display: block;
	}
}

.signal {
	fill: var(--white);
}
</style>
