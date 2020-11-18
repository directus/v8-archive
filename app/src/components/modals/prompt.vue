<template>
	<v-modal-base :title="title" :message="message" @cancel="$emit('cancel')">
		<div class="v-prompt" @keydown.esc="$emit('cancel')">
			<v-textarea
				v-if="multiline"
				class="input multiline"
				autofocus
				:placeholder="placeholder"
				:value="value"
				@input="$emit('input', $event)"
			/>
			<v-input
				v-else
				class="input"
				autofocus
				:value="value"
				:placeholder="placeholder"
				@input="emitValue"
				@keydown.enter="$emit('confirm')"
			/>
			<slot />
			<div class="buttons">
				<button class="cancel" @click="$emit('cancel')">
					{{ cancelText || $t('cancel') }}
				</button>
				<v-button
					class="confirm"
					:loading="loading"
					:disabled="required && disabled"
					@click="$emit('confirm')"
				>
					{{ confirmText || $t('ok') }}
				</v-button>
			</div>
		</div>
	</v-modal-base>
</template>

<script>
import VModalBase from './modal-base.vue';

export default {
	name: 'VPrompt',
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
			required: false
		},
		confirmText: {
			type: String,
			default: null
		},
		cancelText: {
			type: String,
			default: null
		},
		value: {
			type: String,
			default: null
		},
		multiline: {
			type: Boolean,
			default: false
		},
		required: {
			type: Boolean,
			default: false
		},
		placeholder: {
			type: String,
			default: ''
		},
		loading: {
			type: Boolean,
			default: false
		},
		safe: {
			type: Boolean,
			default: false
		}
	},
	computed: {
		disabled() {
			return this.value == null || this.value.length === 0;
		}
	},
	methods: {
		emitValue(val) {
			if (this.safe) {
				val = val
					.toString()
					.replace(/\s+/g, '_') // Replace spaces with _
					.replace(/[^\w_]+/g, '') // Remove all non-word chars
					.replace(/__+/g, '_') // Replace multiple _ with single _
					.replace(/^_+/, '') // Trim _ from start of text
					.replace(/_+$/, '') // Trim _ from end of text
					.toLowerCase();
			}

			this.$emit('input', val);
		}
	}
};
</script>

<style lang="scss" scoped>
.buttons {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-top: 30px;
}
.input {
	margin-top: 20px;

	&.multiline {
		min-height: 100px;
	}
}

.cancel {
	color: var(--button-tertiary-text-color);
	transition: color var(--fast) var(--transition);

	&:hover {
		color: var(--button-tertiary-text-color-hover);
	}
}
</style>
