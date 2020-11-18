<template>
	<div>
		<v-blocker v-show="active" :z-index="9" class="blocker-info" @click="toggle(false)" />
		<aside class="info-sidebar" :class="{ active }">
			<button
				v-tooltip.left="{
					content: $t('info'),
					boundariesElement: 'body'
				}"
				class="sidebar-button"
				@click="toggle(!active)"
			>
				<v-icon icon-style="outline" name="info" color="--sidebar-text-color" />
				<span class="label" v-if="active">{{ $t('info') }}</span>
			</button>

			<div v-if="active" class="content">
				<div class="system">
					<slot name="system" />
				</div>
				<slot />
			</div>

			<router-link
				v-if="canReadActivity"
				v-tooltip.left="{
					content: $t('notifications'),
					boundariesElement: 'body'
				}"
				:to="`/${currentProjectKey}/activity`"
				class="notifications sidebar-button"
			>
				<v-icon name="notifications" color="--sidebar-text-color" />
				<span class="label" v-if="active">{{ $t('notifications') }}</span>
			</router-link>
		</aside>
	</div>
</template>

<script>
import VBlocker from '../blocker.vue';
import { TOGGLE_INFO } from '../../store/mutation-types';
import { mapState, mapMutations } from 'vuex';

export default {
	name: 'InfoSidebar',
	components: {
		VBlocker
	},
	props: {
		itemDetail: {
			type: Boolean,
			default: false
		}
	},
	computed: {
		...mapState({
			currentProjectKey: state => state.currentProjectKey,
			active: state => state.sidebars.info,
			permissions: state => state.permissions
		}),
		canReadActivity() {
			return this.permissions.directus_activity.read !== 'none';
		}
	},
	created() {
		if (this.itemDetail && window.innerWidth > 1235) {
			this.toggle(true);
		}
	},
	methods: {
		...mapMutations({
			toggle: TOGGLE_INFO
		})
	}
};
</script>

<style lang="scss" scoped>
.info-sidebar {
	position: fixed;
	right: 0;
	top: 0;
	height: 100%;
	z-index: 30;
	background-color: var(--sidebar-background-color);
	transform: translateX(220px);
	transition: transform var(--fast) var(--transition);

	&.active {
		transform: translateX(0px);
	}

	& .system:not(:empty) {
		padding-bottom: var(--form-vertical-gap);
		border-bottom: var(--input-border-width) solid var(--input-border-color);
		margin-bottom: var(--form-vertical-gap);
	}

	@media (min-width: 800px) {
		max-width: var(--info-sidebar-width);
	}

	// Overrides for more compressed form
	--form-vertical-gap: 24px;
	--type-label-size: 15px;
	--input-height: 44px;
	--input-font-size: 14px;
	--input-label-margin: 4px;
}

.blocker-info {
	@media (min-width: 1235px) {
		display: none;
	}
}

.content {
	padding: 20px 20px 100px;
	height: 100%;
	overflow: auto;
	-webkit-overflow-scrolling: touch;
}

.sidebar-button {
	width: var(--info-sidebar-width);
	text-decoration: none;
	padding: 20px;
	margin: 0;
	background-color: var(--sidebar-background-color-alt);
	color: var(--sidebar-text-color);
	display: flex;
	align-items: center;

	.label {
		flex-grow: 1;
		margin-left: 10px;
		text-align: left;
	}
}

.notifications {
	position: fixed;
	bottom: 0;
	right: 0;
}
</style>
