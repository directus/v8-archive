<template>
	<div class="not-found">
		<v-header :title="$t('page_not_found')" />
		<v-error
			:title="$t('page_not_found')"
			:body="$t('page_not_found_body')"
			icon="not_interested"
		/>
		<v-info-sidebar wide>
			<span class="type-note">No settings</span>
		</v-info-sidebar>
	</div>
</template>

<script>
import VError from '../components/error.vue';

export default {
	name: 'NotFound',
	metaInfo() {
		return {
			title: this.$t('page_not_found')
		};
	},
	components: {
		VError
	}
};
</script>

<style lang="scss" scoped>
.not-found {
	display: flex;
	justify-content: center;
	align-items: center;
	flex-direction: column;
	min-height: calc(100vh - var(--header-height));
}
</style>
