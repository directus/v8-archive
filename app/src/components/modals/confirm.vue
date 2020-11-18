<template>
	<v-modal-base :title="title" :message="message" @cancel="$emit('cancel')">
		<div class="buttons" @keydown.esc="$emit('cancel')">
			<v-button
				class="cancel"
				outlined
				background-color="--button-tertiary-background-color"
				hover-background-color="--button-tertiary-background-color"
				color="--button-tertiary-text-color"
				hover-color="--button-tertiary-text-color-hover"
				@click="$emit('cancel')"
			>
				{{ cancelText || $t('cancel') }}
			</v-button>
			<v-button
				:background-color="`--${color}`"
				:hover-background-color="`--${color}`"
				class="confirm"
				@click="$emit('confirm')"
			>
				<template v-if="loading">
					<v-spinner :size="20" :line-size="2" />
				</template>
				<template v-else>
					{{ confirmText || $t('ok') }}
				</template>
			</v-button>
		</div>
	</v-modal-base>
</template>

<script>
import VModalBase from './modal-base.vue';

export default {
	name: 'VConfirm',
	components: {
		VModalBase
	},
	props: {
		title: {
			type: String,
			required: false
		},
		message: {
			type: String,
			required: true
		},
		confirmText: {
			type: String,
			default: null
		},
		cancelText: {
			type: String,
			default: null
		},
		loading: {
			type: Boolean,
			default: false
		},
		color: {
			type: String,
			default: 'action'
		}
	}
};
</script>

<style lang="scss" scoped>
.buttons {
	display: flex;
	justify-content: flex-end;
	align-items: center;
	margin-top: 30px;
}

.cancel {
	margin-right: 12px;
}

.v-button:hover {
	opacity: 0.9;
}
</style>
