<template>
	<ul v-if="providers.length > 0" class="sso">
		<li
			v-for="provider in providers"
			:key="provider.name"
			v-tooltip.bottom="{
				classes: ['inverted'],
				content: $helpers.formatTitle(provider.name)
			}"
		>
			<a :href="`${ssoPath}${provider.name}${params}`">
				<img :src="provider.icon" :alt="provider.name" />
			</a>
		</li>
	</ul>
</template>

<script>
import { mapState } from 'vuex';

export default {
	name: 'Sso',
	props: {
		providers: {
			type: Array,
			default: () => []
		}
	},
	computed: {
		...mapState(['currentProjectKey', 'apiRootPath']),
		ssoPath() {
			return this.apiRootPath + this.currentProjectKey + '/auth/sso/';
		},
		params() {
			// %23 == # url encoded
			return '?mode=cookie&redirect_url=' + this.apiRootPath + 'admin/%23/';
		}
	}
};
</script>

<style lang="scss" scoped>
.sso {
	list-style-type: none;
	display: flex;
	justify-content: center;
	align-items: center;
	margin-top: 32px;
	border-top: var(--input-border-width) solid var(--blue-grey-50);
	padding: 32px 0 0 0;

	li {
		width: 40px;
		height: 40px;
		margin: 0 8px;
		background-color: var(--blue-grey-50);
		border-radius: 100%;
		transition: all var(--fast) var(--transition);
		a {
			display: flex;
			justify-content: center;
			align-items: center;
			width: 100%;
			height: 100%;
			img {
				width: 24px;
			}
		}
	}
}
</style>
