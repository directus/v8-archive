<template>
	<v-popover
		class="user-popover"
		trigger="hover"
		:delay="{ show: 300, hide: 0 }"
		:placement="placement"
		:boundaries-element="boundariesElement"
		@show="fetchUser"
	>
		<slot />
		<router-link slot="popover" class="popover" :to="`/${currentProjectKey}/users/${id}`">
			<v-spinner v-if="loading" />
			<template v-else-if="data">
				<v-avatar x-large>
					<img v-if="data.avatar" :src="data.avatar" />
					<v-icon name="person" v-else />
				</v-avatar>
				<div class="info">
					<div class="primary type-label">{{ data.first_name }} {{ data.last_name }}</div>
					<div class="secondary">
						<!-- <v-icon class="icon" name="flag" size="16" /> -->
						{{ data.title || 'No Title' }}
					</div>
					<div class="secondary">
						<!-- <v-icon class="icon" name="room" size="16" /> -->
						{{ data.company || 'No Company' }}
					</div>
				</div>
				<v-icon class="arrow" color="--input-icon-color" name="open_in_new" />
			</template>
		</router-link>
	</v-popover>
</template>

<script>
import { mapState } from 'vuex';

export default {
	name: 'UserPopover',
	props: {
		id: {
			type: Number,
			required: true
		},
		placement: {
			type: String,
			validator(value) {
				return [
					'auto',
					'auto-start',
					'auto-end',
					'top',
					'top-start',
					'top-end',
					'right',
					'right-start',
					'right-end',
					'bottom',
					'bottom-start',
					'bottom-end',
					'left',
					'left-start',
					'left-end'
				].includes(value);
			},
			default: 'top'
		}
	},
	data() {
		return {
			loading: true,
			error: null,
			data: null
		};
	},
	computed: {
		...mapState(['currentProjectKey']),
		boundariesElement() {
			return document.body;
		}
	},
	methods: {
		async fetchUser() {
			// Only fetch once
			if (this.data !== null) return;

			try {
				let { data } = await this.$api.getUser(this.id, {
					fields: [
						'avatar.data.thumbnails',
						'first_name',
						'last_name',
						'title',
						'company',
						'role.name'
					]
				});
				data.role = data.role?.name;
				data.avatar = data?.avatar?.data?.thumbnails?.[0].url;
				this.data = data;
			} catch (error) {
				this.error = error;
			} finally {
				this.loading = false;
			}
		}
	}
};
</script>

<style lang="scss" scoped>
.user-popover {
	width: max-content;
	display: inline-block;
}
.popover-inner {
	padding: 0;
}

.popover {
	padding: 6px 12px;
	width: 280px;
	color: var(--page-text-color);
	display: flex;
	align-items: center;
	text-decoration: none;
	// This fills the gap between the target and popover, so it doesn't lose focus on hover
	&:before {
		content: '';
		position: absolute;
		top: 100%;
		bottom: -12px;
		left: 0;
		right: 0;
	}
	&:hover .arrow {
		color: var(--blue) !important;
	}
	.v-avatar {
		margin-right: 12px;
	}
	.info {
		line-height: 1.4;
		flex-grow: 1;
		max-width: 148px;
		.primary {
			font-weight: var(--weight-bold);
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
		}
		.secondary {
			color: var(--note-text-color);
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
			.icon {
				margin-top: -2px;
				margin-left: -2px;
				margin-right: 4px;
			}
		}
	}
	.arrow {
		flex-basis: 24px;
		width: 24px;
		margin-left: 8px;
	}
}
</style>
