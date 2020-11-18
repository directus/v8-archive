<template>
	<PublicView wide :heading="$t('create_project')">
		<public-stepper class="stepper" :steps="5" :current-step="step" />

		<div v-show="step === 1" class="step-1">
			<template v-if="firstInstall">
				<div class="field-grid">
					<div class="field">
						<h2 class="type-title">{{ $t('welcome_to_directus') }}</h2>
						<p>{{ $t('welcome_to_directus_copy') }}</p>
					</div>
				</div>
				<button type="button" @click="step = 2">{{ $t('next') }}</button>
			</template>
			<template v-else class="field-grid">
				<div class="field-grid">
					<div class="field">
						<h2 class="type-title">{{ $t('create_new_project') }}</h2>
						<p>{{ $t('create_new_project_copy') }}</p>
						<input
							v-model="super_admin_token"
							v-focus
							placeholder="Super-Admin Password..."
							type="text"
						/>
					</div>
				</div>
				<button type="button" @click="step = 2">{{ $t('next') }}</button>
			</template>
		</div>

		<div v-show="step === 2" class="step-2">
			<install-requirements v-if="step === 2" :super-admin-token="super_admin_token" />
			<div class="buttons">
				<span class="secondary" @click="step--">{{ $t('back') }}</span>
				<button type="button" @click="step = 3">{{ $t('next') }}</button>
			</div>
		</div>

		<form v-show="step === 3" class="step-3" @submit.prevent="step = 4">
			<fieldset>
				<legend class="type-title">{{ $t('project_info') }}</legend>
				<div class="field-grid">
					<div class="field">
						<label class="type-label" for="project_name">
							{{ $t('project_name') }}
						</label>
						<input
							id="project_name"
							v-model="project_name"
							v-focus
							name="project_name"
							type="text"
							required
							@input="syncKey"
						/>
					</div>
					<div class="field">
						<label class="type-label" for="project">{{ $t('project_key') }}</label>
						<input
							id="project"
							:value="project"
							name="project"
							type="text"
							required
							pattern="^[0-9a-z_-]+$"
							@input="setProjectKey"
						/>
					</div>
					<div class="field">
						<label class="type-label" for="user_email">{{ $t('admin_email') }}</label>
						<input
							id="user_email"
							v-model="user_email"
							name="user_email"
							type="email"
							required
						/>
					</div>
					<div class="field">
						<label class="type-label" for="user_password">
							{{ $t('admin_password') }}
						</label>
						<input
							id="user_password"
							v-model="user_password"
							class="password"
							name="user_password"
							type="text"
							required
						/>
					</div>
				</div>

				<div class="buttons">
					<span class="secondary" @click="step--">{{ $t('back') }}</span>
					<button type="submit">{{ $t('next') }}</button>
				</div>
			</fieldset>
		</form>

		<form v-show="step === 4" class="step-4" @submit.prevent="onSubmit">
			<fieldset>
				<legend class="type-title">{{ $t('database_connection') }}</legend>
				<div class="field-grid">
					<div class="field">
						<label class="type-label" for="db_host">{{ $t('host') }}</label>
						<input
							id="db_host"
							v-model="db_host"
							v-focus
							name="db_host"
							type="text"
							required
						/>
					</div>
					<div class="field">
						<label class="type-label" for="db_port">{{ $t('port') }}</label>
						<input
							id="db_port"
							v-model="db_port"
							name="db_port"
							type="number"
							required
						/>
					</div>
					<div class="field">
						<label class="type-label" for="db_user">{{ $t('db_user') }}</label>
						<input id="db_user" v-model="db_user" name="db_user" type="text" required />
					</div>
					<div class="field">
						<label class="type-label" for="db_password">{{ $t('db_password') }}</label>
						<input
							id="db_password"
							v-model="db_password"
							class="password"
							name="db_password"
							type="password"
						/>
					</div>
					<div class="field">
						<label class="type-label" for="db_name">{{ $t('db_name') }}</label>
						<input id="db_name" v-model="db_name" name="db_name" type="text" required />
					</div>
					<div class="field">
						<label class="type-label" for="db_type">{{ $t('db_type') }}</label>
						<div class="select">
							<input id="db_type" name="db_type" type="text" value="MySQL" disabled />
						</div>
					</div>
				</div>

				<div class="buttons">
					<span class="secondary" @click="step--">{{ $t('back') }}</span>
					<button type="submit">{{ $t('install') }}</button>
				</div>
			</fieldset>
		</form>

		<div v-show="step === 5" class="step-5">
			<h2 class="type-title">{{ $t('wrapping_up') }}</h2>
			<div class="field-grid">
				<div class="field">
					<v-progress-linear class="progress-bar" rounded indeterminate />
					<p>
						{{ $t('install_busy_copy') }}
					</p>
				</div>
			</div>
		</div>

		<div v-show="step === 6" class="step-6">
			<h2 class="type-title">{{ $t('all_set') }}</h2>
			<div class="field-grid">
				<div class="field">
					<v-progress-linear class="progress-bar" :value="100" rounded />
					<p>
						{{ $t('install_all_set_copy') }}
						<span v-if="firstInstall" class="warning">
							{{ $t('install_all_set_super_admin_password') }}
						</span>
					</p>
					<input v-if="firstInstall" v-model="super_admin_token" type="text" readonly />
					<button type="button" class="button" @click="goToLogin">
						{{ $t('sign_in') }}
					</button>
				</div>
			</div>
		</div>

		<public-notice
			v-if="notice.text"
			slot="notice"
			:loading="false"
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
import axios from 'axios';
import { mapState, mapActions } from 'vuex';
import PublicStepper from '@/components/public/stepper';
import slug from 'slug';
import shortid from 'shortid';
import InstallRequirements from '@/components/install/requirements';

export default {
	name: 'Install',
	components: {
		PublicView,
		PublicNotice,
		PublicStepper,
		InstallRequirements
	},
	data() {
		return {
			step: 1,
			project_name: '',
			project: '',
			user_email: '',
			user_password: '',
			db_host: 'localhost',
			notice: {
				text: this.$t('project_not_configured'),
				color: 'blue-grey-100',
				icon: 'outlined_flag'
			},
			db_port: 3306,
			db_user: '',
			db_password: '',
			db_name: '',
			installing: false,
			error: null,
			manualKey: false,
			super_admin_token: '',
			adminTokenValid: false,
			fetchingRequirements: false
		};
	},
	computed: {
		...mapState(['apiRootPath', 'projects']),
		firstInstall() {
			return this.projects === false;
		}
	},
	methods: {
		...mapActions(['getProjects']),
		generateMasterPassword() {
			const sections = 2;
			let password = '';
			for (let i = 0; i <= sections; i++) {
				password += shortid.generate();
			}
			return password;
		},
		async onSubmit() {
			// When you hit enter on the first page, we don't want to submit the install data, instead
			// we go to the second page
			if (this.step === 3) {
				this.step = 4;
				return;
			}

			this.step = 5;

			// We want the install to at least take 3 seconds before being done, to make the user feel like
			// the installer is actually doing things. This will make sure 3 seconds have passed before we
			// go to the confirmation of done.
			const next = () => {
				this.$notify({
					title: this.$t('api_installed'),
					color: 'green',
					iconMain: 'check'
				});

				this.step = 6;
			};

			let installReady = false;
			let timeReady = false;

			setTimeout(() => {
				timeReady = true;

				if (installReady && timeReady) next();
			}, 4000);

			const {
				project_name,
				project,
				user_email,
				user_password,
				db_host,
				db_port,
				db_user,
				db_password,
				db_name
			} = this;

			try {
				if (this.firstInstall === true) {
					this.super_admin_token = this.generateMasterPassword();
				}

				await axios.post(this.apiRootPath + 'server/projects', {
					project_name,
					project,
					user_email,
					user_password,
					db_host,
					db_port,
					db_user,
					db_password,
					db_name,
					super_admin_token: this.super_admin_token
				});

				installReady = true;

				if (installReady && timeReady) {
					next();
				}
			} catch (error) {
				this.error = error;

				console.error(error);

				this.$events.emit('error', {
					notify: error.response?.data?.error?.message,
					error
				});

				this.step = 4;
			}
		},
		syncKey() {
			if (this.manualKey === false) {
				this.project = slug(this.project_name, { lower: true });
			}
		},
		setProjectKey(event) {
			if (this.manualKey === false) this.manualKey = true;
			const value = slug(event.target.value, { lower: true });
			this.project = value;
		},
		async goToLogin() {
			await this.getProjects(true);

			this.$router.push('/login', { query: { project: this.project } });
		}
	}
};
</script>

<style lang="scss" scoped>
// NOTE: These button and input styles are copied from login.vue and should be extracted to a base component

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

.select {
	position: relative;
	&:after {
		content: 'arrow_drop_down';
		font-family: var(--family-icon);
		position: absolute;
		right: 12px;
		top: 8px;
		color: var(--input-icon-color);
	}
}

input {
	position: relative;
	width: 100%;
	margin-bottom: 32px;
	border: 0;
	font-size: 16px;
	border: var(--input-border-width) solid var(--input-border-color);
	width: 100%;
	height: 64px;
	padding: 20px 10px;
	background-color: var(--input-background-color);
	color: var(--darker-gray);
	transition: border-color var(--fast) var(--transition);
	border-radius: var(--border-radius);
	font-family: var(--family-monospace);

	&::placeholder {
		color: var(--light-gray);
	}

	&:-webkit-autofill {
		color: var(--darker-gray) !important;
		-webkit-text-fill-color: var(--darker-gray);
		-webkit-box-shadow: 0 0 0px 1000px var(--white) inset;
	}

	&:hover:not([disabled]) {
		transition: none;
		border-color: var(--gray);
		&:focus {
			border-color: var(--darker-gray);
		}
	}

	&[disabled] {
		cursor: not-allowed;
		background-color: var(--input-background-color-disabled);
	}

	&:focus {
		outline: 0;
		border-color: var(--darker-gray);

		&:-webkit-autofill {
			color: var(--darker-gray) !important;
			-webkit-text-fill-color: var(--darker-gray);
			-webkit-box-shadow: 0 0 0px 1000px var(--white) inset;
		}
	}
}

///////////////////////////////////

.buttons {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-top: 16px;
	.secondary {
		transition: color var(--fast) var(--transition);
		text-decoration: none;
		color: var(--input-placeholder-color);
		cursor: pointer;
		font-size: 16px;
		&:hover {
			color: var(--page-text-color);
		}
	}
}

p {
	font-size: 16px;
	line-height: 26px;
	margin-top: 32px;
	margin-bottom: 32px;
	color: var(--blue-grey-300);
}

.stepper {
	margin-top: 4px;

	@media (min-height: 800px) {
		margin-top: 24px;
	}
}

legend {
	margin-bottom: 20px;
}

.stepper {
	margin-bottom: 64px;
	max-width: 320px;
}

.progress-bar {
	margin-top: 32px;
}

.progress-bar-complete {
	margin-top: 32px;
	width: 100%;
	background-color: var(--progress-background-color-accent);
	position: relative;
	height: 4px;
	border-radius: var(--border-radius);
}

.warning {
	color: var(--warning);
}

.field-grid {
	display: grid;
	grid-template-columns: repeat(2, 1fr);
	grid-gap: 8px 32px;
}

label {
	margin-bottom: 8px;
}

// There is no way to currently disable the browser from offering to save the password. We do not want the user to be
// bothered by the browser asking to save the database password. This is the only way to hack around it. By using text
// instead of password for type, we can trick the browser into thinking this is in fact not a password 🤦
.password {
	-moz-text-security: disc;
	-webkit-text-security: disc;
	text-security: disc;
}
</style>
