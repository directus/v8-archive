<template>
	<v-modal-base :message="message" @cancel="$emit('confirm')">
		<div class="buttons" @keydown.esc="$emit('confirm')">
			<v-button class="confirm" @click="$emit('confirm')">
				{{ confirmText || $t('ok') }}
			</v-button>
		</div>
	</v-modal-base>
</template>

<script>
import VModalBase from './modal-base.vue';

export default {
	name: 'VAlert',
	components: {
		VModalBase
	},
	props: {
		message: {
			type: String,
			required: true
		},
		confirmText: {
			type: String,
			default: null
		}
	}
};
</script>

<style lang="scss" scoped>
.buttons {
	margin-top: 32px;

	.confirm {
		float: right;
	}
}
</style>
