<template>
	<div class="interface-2fa-value">
		<v-notice v-if="tfa_secret" icon="info" class="qr-info">
			{{ $t('scan_in_authenticator') }}
		</v-notice>
		<qr-code v-if="tfa_secret" class="qr" :value="totpUrl" :options="{ width: 200 }" />

		<v-button v-if="!value" :loading="loading" @click="getToken">Enable 2FA</v-button>
		<v-button v-if="value" @click="removeValue">Disable 2FA</v-button>
	</div>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';
import QrCode from '@chenfengyuan/vue-qrcode';

export default {
	name: 'Interface2faValue',
	components: {
		QrCode
	},
	mixins: [mixin],
	data() {
		return {
			loading: false,
			error: null,
			tfa_secret: null
		};
	},
	computed: {
		totpUrl() {
			return `otpauth://totp/Directus:${this.$store.state.currentUser.email}?secret=${this.tfa_secret}&issuer=Directus`;
		}
	},
	methods: {
		getToken() {
			this.loading = true;

			this.$api.api
				.get('/utils/2fa_secret')
				.then(res => res.data['2fa_secret'])
				.then(token => {
					this.$emit('input', token);
					this.tfa_secret = token;
				})
				.catch(error => {
					this.error = error;
				})
				.finally(() => (this.loading = false));
		},
		removeValue() {
			this.$emit('input', null);
			this.tfa_secret = null;
		}
	}
};
</script>

<style scoped>
.qr {
	border: var(--input-border-width) solid var(--blue-grey-50);
	border-radius: var(--border-radius);
	margin-bottom: 16px;
}

.qr-info {
	margin-bottom: 16px;
}
</style>
