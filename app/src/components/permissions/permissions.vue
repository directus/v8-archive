<template>
	<div v-if="loading" class="v-permissions interface loading">
		<v-spinner color="--blue-grey-300" background-color="--blue-grey-200" />
	</div>
	<div v-else class="interface">
		<div class="v-permissions">
			<v-permissions-header @toggle-all="toggleAll" />

			<div class="body">
				<v-notice
					v-if="Object.keys(rows).length === 0"
					color="gray"
					class="no-collections-message"
				>
					{{ $t('permissions_no_collections') }}
				</v-notice>

				<v-permissions-row
					v-for="(permission, name) in rows"
					:key="name"
					:permission="permission"
					:permission-name="name"
					:statuses="(statuses[name] || {}).mapping"
					:fields="fields[name]"
					@input="$emit('input', $event)"
				/>

				<template v-if="showDirectus">
					<v-permissions-row
						v-for="(permission, name) in directusRows"
						:key="name"
						:permission="permission"
						:permission-name="name"
						:statuses="(statuses[name] || {}).mapping"
						:fields="fields[name]"
						system
						@input="$emit('input', $event)"
					/>
				</template>
			</div>
		</div>
		<v-switch v-model="showDirectus" :label="$t('show_directus_collections')" />
	</div>
</template>

<script>
import VPermissionsHeader from './permissions-header.vue';
import VPermissionsRow from './permissions-row.vue';
import { pickBy, forEach } from 'lodash';

export default {
	name: 'VPermissions',
	components: {
		VPermissionsHeader,
		VPermissionsRow
	},
	props: {
		permissions: {
			type: Object,
			default: () => ({})
		},
		statuses: {
			type: Object,
			default: () => ({})
		},
		fields: {
			type: Object,
			default: () => ({})
		},
		loading: {
			type: Boolean,
			default: false
		}
	},
	data() {
		return {
			showDirectus: false
		};
	},
	computed: {
		directusRows() {
			const permissions = pickBy(this.permissions, (permission, collection) =>
				collection.startsWith('directus_')
			);

			return _(permissions)
				.toPairs()
				.sortBy(0)
				.fromPairs()
				.value();
		},
		rows() {
			const permissions = pickBy(
				this.permissions,
				(permission, collection) => collection.startsWith('directus_') === false
			);

			return _(permissions)
				.toPairs()
				.sortBy(0)
				.fromPairs()
				.value();
		}
	},
	methods: {
		toggleAll(permission) {
			const changes = [];
			let full = true;

			forEach(this.permissions, (column, collection) => {
				if (collection.startsWith('directus_')) return;
				if (this.statuses[collection]) {
					forEach(column, statusColumn => {
						if (statusColumn[permission] === 'full') {
							full = false;
						}
					});
					return;
				}

				if (column[permission] === 'full') {
					full = false;
				}
			});

			Object.keys(this.permissions).forEach(collection => {
				if (collection.startsWith('directus_')) return;

				if (this.statuses[collection]) {
					return Object.keys(this.statuses[collection].mapping).forEach(status => {
						changes.push({
							collection,
							status,
							permission,
							value: full ? 'full' : 'none'
						});
					});
				}

				changes.push({
					collection,
					permission,
					value: full ? 'full' : 'none'
				});
			});

			this.$emit('input', changes);
		}
	}
};
</script>
<style lang="scss" scoped>
.interface {
	margin-bottom: 40px;
}
.v-permissions {
	background-color: var(--page-background-color);
	border-radius: var(--border-radius);
	border: var(--input-border-width) solid var(--input-border-color);
	max-width: 632px;
	margin-bottom: 16px;

	.no-collections-message {
		margin-top: 20px;
		margin-bottom: 40px;
	}

	::v-deep .body .row {
		display: flex;
		align-items: center;
		padding: 10px;
		height: 40px;
		&.sub {
			position: relative;
			&::before {
				content: 'call_missed_outgoing';
				font-family: 'Material Icons';
				position: absolute;
				top: 8px;
				transform: rotate(45deg);
				font-size: 18px;
				color: var(--input-icon-color);
				font-feature-settings: 'liga';
			}
			& .cell:first-child {
				padding-left: 2rem;
			}
		}
		&:not(.sub) {
			border-top: var(--input-border-width) solid var(--table-row-border-color);
		}
	}
	::v-deep .cell {
		flex-basis: 36px;
		&:first-child {
			flex-basis: 220px;
			max-width: 220px;
			overflow: hidden;
		}
		&:nth-last-child(3) {
			flex-basis: 52px;
		}
		&:nth-last-child(2) {
			flex-basis: 92px;
		}
		&:last-child {
			flex-basis: 100px;
		}
	}

	&.loading {
		padding: 300px 0;
		text-align: center;

		.v-spinner {
			display: inline-block;
		}
	}
}
</style>
