<template>
	<div class="display-rating">
		<!-- If 'number' is selected for display option -->
		<div v-if="options.display == 'number'" class="rating-numbers">
			<v-icon name="star" />
			<span>{{ value ? value : 0 }}</span>
		</div>

		<!-- If 'star' is selected for display option -->
		<div v-if="options.display == 'star'" class="rating-stars">
			<stars :options="options" :rating="value" :readonly="true" :small="true"></stars>
		</div>
	</div>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';
import Stars from './stars.vue';

export default {
	components: {
		Stars
	},
	mixins: [mixin]
};
</script>

<style lang="scss" scoped>
.rating-stars,
.rating-numbers {
	display: flex;
	align-items: center;
	i.material-icons {
		font-size: 18px;
		line-height: 1;
		margin-right: 4px;
	}
}
</style>
