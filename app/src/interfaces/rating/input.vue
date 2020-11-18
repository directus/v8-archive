<template>
	<div class="rating">
		<stars
			v-if="options.display === 'star'"
			:options="options"
			:rating.sync="!!value ? value : rating"
			:readonly="readonly"
			@update:rating="updateValue"
		></stars>
		<div v-else-if="options.display === 'number'" class="rating-value">
			<v-input
				class="rating-input"
				type="number"
				min="0"
				:max="options.max_stars"
				icon-left="star"
				:disabled="readonly"
				:value="String(value) || '0'"
				@input="updateValue"
			></v-input>
			<span>
				{{ (!!value ? String(value) : '0') + '/' + options.max_stars }}
			</span>
		</div>
	</div>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';
import Stars from './stars.vue';

export default {
	name: 'InterfaceRating',
	components: {
		Stars
	},
	mixins: [mixin],
	data() {
		return {
			rating: null
		};
	},
	methods: {
		updateValue(value) {
			if (value > this.options.max_stars) {
				event.target.value = String(this.options.max_stars);
				return this.$emit('input', this.options.max_stars);
			}

			this.$emit('input', +value);
		}
	}
};
</script>

<style lang="scss" scoped>
.rating-value {
	position: relative;
	display: flex;
	.rating-input {
		display: inline-block;
		margin-right: 8px;
		width: 100%;
	}
	span {
		padding: 13px 12px;
		background-color: var(--blue-grey-50);
		border-radius: var(--border-radius);
	}
}
</style>
