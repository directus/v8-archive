<template>
	<PublicView :heading="$t('setup_2fa')">
		<template v-if="finished === false && !fetchingData">
			<!-- <h2 class="type-title">Scan the QR Code</h2> -->
			<p>
				Scan this QR code with your authenticator app, then enter the verification code it
				provides to complete setup.
			</p>
			<v-spinner v-if="!tfa_secret" />
			<qr-code v-else class="qr" :value="totpUrl" :options="{ width: 340 }" />
			<otp-input @input="save2faSecret" />
		</template>
		<template v-if="finished === true && !fetchingData">
			<p>
				Congratulations! You have successfully set up 2-factor authentication for Directus.
			</p>
			<button type="button" @click="enterApp">Continue</button>
		</template>
		<v-progress-linear v-if="saving || fetchingData" class="loading" indeterminate rounded />
		<public-notice v-if="error" slot="notice" color="danger">
			{{ $t(`errors.${error.code}`) }}
		</public-notice>
	</PublicView>
</template>

<script>
import PublicView from '@/components/public-view';
import PublicNotice from '@/components/public/notice';
import QrCode from '@chenfengyuan/vue-qrcode';
import { mapState } from 'vuex';
import OtpInput from '@/components/public/otp-input';
import hydrateStore from '@/hydrate';

// NOTE: We'll have to use tfa instead of 2fa in JavaScript. Variables can't start with a number

export default {
	name: 'Setup2FA',
	components: {
		PublicView,
		PublicNotice,
		QrCode,
		OtpInput
	},
	data() {
		return {
			tfa_secret: null,
			error: null,
			finished: false,
			saving: false,
			fetchingData: false
		};
	},
	computed: {
		...mapState(['apiRootPath', 'currentProjectKey']),
		totpUrl() {
			return `otpauth://totp/Directus:${this.$store.state.currentUser.email}?secret=${this.tfa_secret}&issuer=Directus`;
		}
	},
	created() {
		this.fetch2faSecret();
	},
	methods: {
		async fetch2faSecret() {
			const response = await this.$api.api.get('/utils/2fa_secret');
			this.tfa_secret = response.data['2fa_secret'];
		},
		async save2faSecret(otp) {
			this.saving = true;
			const body = {
				['2fa_secret']: this.tfa_secret,
				otp: otp
			};

			try {
				await this.$api.api.post('/users/me/activate_2fa', body);
				this.finished = true;
			} catch (error) {
				this.error = error;
				console.error(error);
			} finally {
				this.saving = false;
			}
		},
		async enterApp() {
			this.fetchingData = true;

			// This will fetch all the needed information about the project in order to run Directus
			await hydrateStore();

			// Default to /collections as homepage
			let route = `/${this.currentProjectKey}/collections`;

			// If the last visited page is saved in the current user record, use that
			if (this.$store.state.currentUser.last_page) {
				route = this.$store.state.currentUser.last_page;
			}

			// In the case the URL contains a redirect query, use that instead
			if (this.$route.query.redirect) {
				route = this.$route.query.redirect;
			}

			this.$router.push(route, () => {
				// We only set the fetchingData flag to false when the page navigation is done
				// This makes sure we don't show a flash of "authenticated" style login view
				this.fetchingData = false;
			});
		}
	}
};
</script>

<style lang="scss">
.qr {
	margin-bottom: 32px;
	border: var(--input-border-width) solid var(--input-border-color);
	border-radius: var(--border-radius);
}
</style>

<style lang="scss" scoped>
p {
	font-size: 16px;
	line-height: 26px;
	margin-top: 32px;
	margin-bottom: 32px;
	color: var(--blue-grey-300);
}

.loading {
	margin-top: 32px;
	margin-bottom: 32px;
}

.button,
button {
	position: relative;
	background-color: var(--button-primary-background-color);
	border: 2px solid var(--button-primary-background-color);
	border-radius: var(--border-radius);
	color: var(--button-primary-text-color);
	height: 60px;
	padding: 18px 20px;
	width: 100%;
	max-width: 154px;
	font-size: 16px;
	font-weight: 400;
	transition: background-color var(--fast) var(--transition);
	display: inline-block;
	text-decoration: none;
	text-align: center;

	&[disabled] {
		cursor: not-allowed;
	}

	&:not([disabled]) {
		&:hover {
			background-color: var(--darkest-gray);
			border-color: var(--darkest-gray);
		}
	}

	&.outline {
		background-color: transparent;
		color: var(--darkest-gray);

		&[disabled] {
			background-color: transparent;
		}

		&:not([disabled]) {
			&:hover {
				background-color: transparent;
			}
		}
	}
}
</style>
