<template>
	<PublicView :heading="signingIn || fetchingData ? $t('signing_in') : $t('sign_in')">
		<form @submit.prevent="onSubmit">
			<project-chooser v-if="(signingIn || fetchingData) === false" />

			<template v-if="signingIn || fetchingData">
				<v-progress-linear rounded indeterminate />
				<p>{{ currentProject.project_name }}</p>
			</template>

			<!-- the getProjects action will set the currentProject on load. When currentProject doesn't exist
        it means that the store doesn't have any projects that can be used loaded-->
			<template v-else-if="!currentProject">
				<v-notice icon="info" color="warning">{{ $t('no_public_projects') }}</v-notice>
			</template>

			<template v-else-if="currentProject.status === 'failed'">
				Something is wrong with this project
				<!-- TODO: use v-error here -->
				<v-notice icon="error" color="danger">{{ readableError }}</v-notice>
			</template>

			<template v-else-if="needs2fa === true">
				<p>{{ $t('enter_otp') }}</p>
				<otp-input @input="onOTPInput" />
			</template>

			<template v-else>
				<div
					v-if="
						currentProject.status === 'successful' &&
							currentProject.data.authenticated === true
					"
				>
					<v-spinner v-if="firstName === null" />
					<p v-else v-html="$t('continue_as', { name: firstName + ' ' + lastName })" />
					<div class="buttons">
						<button type="button" class="secondary" @click="logout">
							{{ $t('sign_out') }}
						</button>
						<button type="submit">{{ $t('continue') }}</button>
					</div>
				</div>
				<template v-else>
					<input
						v-model="email"
						v-focus
						type="email"
						:placeholder="$t('email')"
						required
						autocomplete="username"
					/>
					<input
						ref="password"
						v-model="password"
						type="password"
						:placeholder="$t('password')"
						autocomplete="current-password"
						required
					/>
					<div class="buttons">
						<button type="submit">{{ $t('sign_in') }}</button>
						<router-link class="secondary" to="/reset-password">
							{{ $t('forgot_password') }}
						</router-link>
					</div>
					<sso :providers="ssoProviders" />
				</template>
			</template>
		</form>
		<public-notice
			v-if="notice.text"
			slot="notice"
			:loading="signingIn || fetchingData"
			:color="notice.color"
			:icon="notice.icon"
		>
			{{ notice.text }}
		</public-notice>
	</PublicView>
</template>

<script>
import PublicView from '@/components/public-view';
import PublicNotice from '@/components/public/notice';
import Sso from '@/components/public/sso';
import ProjectChooser from '@/components/public/project-chooser';
import { mapState, mapGetters, mapMutations } from 'vuex';
import { UPDATE_PROJECT } from '@/store/mutation-types';
import hydrateStore from '@/hydrate';
import OtpInput from '@/components/public/otp-input';
import { clone } from 'lodash';

export default {
	name: 'Login',
	components: {
		PublicView,
		PublicNotice,
		ProjectChooser,
		Sso,
		OtpInput
	},
	data() {
		return {
			email: '',
			password: '',
			otp: '',
			signingIn: false,
			fetchingData: false,
			notice: {
				text: this.$t('not_authenticated'),
				color: 'blue-grey-100',
				icon: 'lock_outline'
			},
			firstName: null,
			lastName: null,
			ssoProviders: [],
			needs2fa: false
		};
	},
	computed: {
		...mapGetters(['currentProject']),
		...mapState(['currentProjectKey', 'apiRootPath', 'projects']),
		readableError() {
			if (this.currentProject?.status !== 'failed') return null;
			return (
				this.currentProject.error.response?.data?.error?.message ||
				this.currentProject.error.message
			);
		}
	},
	watch: {
		currentProject: {
			deep: true,
			handler() {
				this.handleLoad();
			}
		}
	},
	created() {
		this.handleLoad();
		this.checkForErrorQueryParam();
	},
	methods: {
		...mapMutations([UPDATE_PROJECT]),
		onSubmit() {
			if (this.currentProject?.data?.authenticated) {
				return this.enterApp();
			} else {
				return this.login();
			}
		},
		login() {
			const { email, password } = this;
			this.signingIn = true;

			this.notice = {
				text: this.$t('signing_in'),
				color: 'blue-grey',
				icon: null
			};

			const credentials = {
				project: this.currentProjectKey,
				email,
				password,
				mode: 'cookie'
			};

			if (this.otp && this.otp.length === 6) {
				credentials.otp = this.otp;
			}

			this.$api
				.login(credentials)
				.then(async response => {
					// There's one specific case where we expect a successful response (200) to contain an error: 2FA
					// When 2FA is required but not enabled for the current user, they can
					// still log in, but the token will only work for /users/me
					if (response.error) {
						throw {
							info: {
								code: response.error.code
							}
						};
					}

					const { data: projectInfo } = await this.$api.api.get('/');
					const { requires2FA, version, database } = projectInfo.api;
					const { max_upload_size } = projectInfo.server;

					this[UPDATE_PROJECT]({
						key: this.currentProjectKey,
						data: {
							authenticated: true,
							requires2FA,
							version,
							database,
							max_upload_size
						}
					});

					this.enterApp();

					this.signingIn = false;
				})
				.catch(response => {
					this.signingIn = false;
					const code = response.info?.code;

					if (code === 111) {
						this.needs2fa = true;

						this.notice = {
							text: this.$t(`errors.${code}`),
							color: 'blue-grey-100',
							icon: 'lock_outline'
						};
					} else if (code === 113) {
						this.$router.push('/setup-2fa');
					} else if (code === 100) {
						this.notice = {
							text: this.$t(`errors.${code}`),
							color: 'warning',
							icon: 'warning'
						};

						this.$nextTick(() => {
							this.$refs.password.select();
						});
					} else if (code) {
						this.notice = {
							text: this.$t(`errors.${code}`),
							color: 'warning',
							icon: 'warning'
						};
					} else {
						this.notice = {
							text: this.$t(`errors.-1`),
							color: 'danger',
							icon: 'error_outline'
						};
					}
				});
		},
		async logout() {
			await this.$api.logout();
			this.$store.commit(UPDATE_PROJECT, {
				key: this.$store.state.currentProjectKey,
				data: {
					authenticated: false
				}
			});
		},
		async enterApp() {
			this.notice = {
				text: this.$t('fetching_data')
			};

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
		},
		async fetchAuthenticatedUser() {
			if (!this.currentProject) return;
			this.firstName = null;
			this.lastName = null;
			const { data } = await this.$api.getMe({ fields: ['first_name', 'last_name'] });
			this.firstName = data.first_name;
			this.lastName = data.last_name;
		},
		async fetchSSOProviders() {
			if (!this.currentProject) return;
			this.ssoProviders = [];
			const { data } = await this.$api.getThirdPartyAuthProviders();
			this.ssoProviders = data;
		},
		onOTPInput(value) {
			this.otp = value;
			this.login();
		},
		checkForErrorQueryParam() {
			if (this.$route.query.error) {
				this.notice = {
					text: this.$t(`errors.${this.$route.query.code}`),
					color: 'danger',
					icon: 'error'
				};

				// Remove query params
				const query = clone(this.$route.query);
				delete query.error;
				delete query.code;
				this.$router.replace({ query });
			}
		},
		handleLoad() {
			if (this.currentProject?.status === 'successful') {
				if (this.currentProject?.data?.authenticated === true) {
					this.fetchAuthenticatedUser();
				} else {
					this.fetchSSOProviders();
				}
			}
		}
	}
};
</script>

<style lang="scss" scoped>
// TODO: These styles should be extraced into their base components
//       They're currently duplicated on forgot-password, setup-2fa, and install
//       as well
form {
	margin-top: 32px;

	@media (min-height: 800px) {
		margin-top: 52px;
	}
}

.button,
button:not(.secondary) {
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

p {
	font-size: 16px;
	line-height: 26px;
	margin-top: 32px;
	margin-bottom: 32px;
	color: var(--blue-grey-300);

	::v-deep b {
		font-weight: var(--weight-bold);
		color: var(--page-text-color);
	}
}

input {
	position: relative;
	width: 100%;
	margin-bottom: 32px;
	border: 0;
	font-size: 16px;
	border: 2px solid var(--input-border-color);
	width: 100%;
	padding: 20px 10px;
	color: var(--input-text-color);
	transition: border-color var(--fast) var(--transition);
	border-radius: var(--border-radius);

	&::placeholder {
		color: var(--input-placeholder-color);
	}

	&:-webkit-autofill {
		color: var(--input-text-color) !important;
		-webkit-text-fill-color: var(--input-text-color);
		-webkit-box-shadow: 0 0 0px 1000px var(--white) inset;
	}

	&:hover:not([disabled]) {
		transition: none;
		border-color: var(--input-border-color-hover);
		&:focus {
			border-color: var(--input-border-color-focus);
		}
	}

	&[disabled] {
		cursor: not-allowed;
	}

	&:focus {
		outline: 0;
		border-color: var(--input-border-color-focus);

		&:-webkit-autofill {
			color: var(--input-text-color) !important;
			-webkit-text-fill-color: var(--input-text-color);
			-webkit-box-shadow: 0 0 0px 1000px var(--white) inset;
		}
	}
}

.buttons {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-top: 8px;

	.secondary {
		transition: color var(--fast) var(--transition);
		flex-shrink: 0;
		// margin-left: 24px; // Not when on left ("continue as")
		text-decoration: none;
		font-size: 16px;
		cursor: pointer;
		color: var(--input-placeholder-color);
		&:hover {
			color: var(--page-text-color);
		}
	}
}
</style>
