<template>
	<div class="v-upload" :class="{ uploading, disabled }">
		<input
			ref="select"
			:disabled="disabled"
			class="select"
			type="file"
			:accept="accept"
			:multiple="multiple"
			@change="filesChange($event.target.files)"
		/>

		<div class="dropzone" :class="{ smaller: small }">
			<div class="icon">
				<v-icon
					name="cloud_upload"
					:size="uploading ? 32 : 100"
					color="--input-icon-color"
				/>
			</div>
			<div class="info">
				<p class="name type-heading-small">{{ $tc('drop_files', multiple ? 2 : 1) }}</p>
				<p class="file-info no-wrap">
					{{
						$t('max_size', {
							size: $helpers.filesize($store.state.serverInfo.maxUploadSize)
						})
					}}
				</p>
			</div>
			<div class="buttons">
				<form v-if="embed" class="embed-input" @submit.prevent="saveEmbed">
					<input v-model="embedLink" type="url" :placeholder="$t('embed_placeholder')" />
					<button type="submit">Save</button>
				</form>
				<button @click="embed = !embed">
					<v-icon v-tooltip="$t('embed')" name="link" class="select" />
				</button>
				<button @click="$refs.select.click()">
					<v-icon
						v-tooltip="$t('select_from_device')"
						class="material-icons select"
						name="devices"
					/>
				</button>
			</div>
		</div>
		<transition-group tag="ol" name="list">
			<li v-for="file in files" :key="file.id" class="list-item">
				<v-progress-ring
					class="icon"
					:progress="file.progress"
					:icon="
						file.error !== null
							? 'cloud_off'
							: file.progress === 100
							? 'cloud_done'
							: 'cloud_upload'
					"
					:color="
						file.error !== null
							? 'danger'
							: file.progress === 100
							? 'success'
							: 'accent'
					"
					:stroke="file.progress === 100 ? 0 : 2"
				/>
				<div class="info">
					<p class="name no-wrap">{{ file.name }}</p>
					<p class="file-info no-wrap">
						{{ file.size }}
						<span v-if="file.progress && file.progress !== 100" class="progress">
							{{ file.progress }}%
						</span>
					</p>
				</div>
			</li>
		</transition-group>

		<input
			ref="drop"
			:disabled="disabled"
			class="drop"
			type="file"
			:accept="accept"
			:multiple="multiple"
			@click.prevent
			@change="filesChange($event.target.files)"
		/>
	</div>
</template>

<script>
import filesize from 'filesize';

export default {
	name: 'VUpload',
	props: {
		accept: {
			type: String
		},
		multiple: {
			type: Boolean,
			default: true
		},
		small: {
			type: Boolean,
			default: false
		},
		disabled: {
			type: Boolean,
			default: false
		}
	},
	data() {
		return {
			files: {},
			embedLink: null,
			embed: false
		};
	},
	computed: {
		acceptTypesList() {
			if (this.accept) {
				return this.accept.trim().split(/\s*,\s*/);
			} else {
				return [];
			}
		},
		uploading() {
			return Object.keys(this.files).length > 0;
		}
	},
	methods: {
		saveEmbed() {
			const id = this.$helpers.shortid.generate();
			const name = this.embedLink.substring(this.embedLink.lastIndexOf('/') + 1);
			this.files = {
				[id]: {
					id,
					name,
					size: null,
					progress: 0,
					type: null,
					error: null
				},
				...this.files
			};
			this.$api
				.createItem('directus_files', {
					data: this.embedLink
				})
				.then(res => res.data)
				.then(data => {
					const { filesize: size, type, title: name } = data;
					this.files = {
						[id]: {
							id,
							name,
							size,
							progress: 100,
							type,
							error: null
						},
						...this.files
					};
					return data;
				})
				.then(data => {
					this.$emit('upload', {
						...this.files[id],
						data
					});
				})
				.then(() => (this.embed = false))
				.then(() => (this.embedLink = null))
				.catch(error => {
					this.$events.emit('error', {
						notify: this.$t('something_went_wrong_body'),
						error
					});
				});
		},

		filesChange(fileList) {
			if (!fileList || !fileList.length) return;

			Array.from(fileList).forEach(this.save);
		},
		save(file) {
			const id = this.$helpers.shortid.generate();
			const formData = new FormData();
			const { name, size, type } = file;

			if (size !== -1 && size > this.$store.state.serverInfo.maxUploadSize) {
				this.$events.emit('warning', {
					notify: this.$t('upload_exceeds_max_size', { filename: name })
				});

				return;
			}

			if (this.acceptTypesList.length > 0 && !this.acceptTypesList.includes(type)) {
				this.$events.emit('warning', {
					notify: this.$t('file_type_not_accepted', { filename: name })
				});

				return;
			}

			formData.append('data', file, name);

			this.files = {
				[id]: {
					id,
					name,
					size: filesize(size),
					type,
					progress: 0,
					error: null
				},
				...this.files
			};

			this.$api
				.uploadFiles(formData, ({ loaded, total }) => {
					const progress = Math.min(Math.round((loaded * 100) / total), 95);
					this.files[id].progress = progress;
				})
				.then(res => res.data)
				.then(res => {
					this.files[id].progress = 100;
					this.$emit('upload', {
						...this.files[id],
						data: res
					});

					// reset the inputs
					this.$refs.select.value = '';
					this.$refs.drop.value = '';
				})
				.catch(error => {
					this.files[id].error = error;
					let message;
					if (error.message) {
						message = error.message;
					} else {
						message = this.$t('something_went_wrong_body');
					}
					this.$events.emit('error', {
						notify: message,
						error
					});
				});
		}
	}
};
</script>

<style lang="scss" scoped>
.v-upload {
	position: relative;
	background-color: var(--page-background-color);
	width: 100%;
	height: var(--width-medium);

	&.disabled {
		background-color: var(--page-background-color);
		cursor: not-allowed;
	}
}

.dropzone {
	position: relative;
	padding: 48px 0;

	.buttons {
		position: absolute;
		top: 22px;
		right: 20px;
		display: flex;
		align-items: center;
		height: 26px;

		> * {
			cursor: pointer;
			transition: color var(--fast) var(--transition);
			user-select: none;
			margin-left: 10px;

			&:hover {
				transition: none;
			}
		}

		.embed-input {
			display: flex;
			align-items: center;
			position: relative;

			input {
				border-radius: var(--border-radius);
				border: 1px solid var(--blue-grey-200);
				padding: 4px;
				color: var(--blue-grey-600);

				&::placeholder {
					color: var(--blue-grey-200);
				}

				padding-right: 40px;
				width: 250px;
			}

			button {
				width: 40px;
				position: absolute;
				right: 0;
				height: 100%;
				background-color: var(--blue-grey-300);
				border-top-right-radius: var(--border-radius);
				border-bottom-right-radius: var(--border-radius);
				color: var(--white);

				&:hover {
					background-color: var(--blue-grey-900);
					color: var(--white);
				}
			}
		}
	}
}

input.drop {
	opacity: 0;
	position: absolute;
	left: 0;
	top: 0;
	width: 100%;
	height: 100%;
	pointer-events: none;
}

input.select {
	display: none;
}

.v-upload:not(.uploading) .dropzone {
	color: var(--page-text-color);
	height: 100%;
	display: flex;
	justify-content: center;
	align-items: center;
	flex-direction: column;
	user-select: none;
	transition: var(--fast) var(--transition);
	transition-property: border-color, color;
	border: var(--input-border-width) dashed var(--input-border-color);
	border-radius: var(--border-radius);

	p {
		color: currentColor;
	}

	.info {
		text-align: center;
		color: var(--input-border-color);
	}

	.file-info {
		text-align: center;
		color: var(--input-border-color);
		margin-top: 4px;
	}

	.buttons > * {
		color: var(--input-border-color);

		&:hover {
			color: var(--page-text-color);
		}
	}

	.dragging & {
		transition: border-color var(--slow) var(--transition);
		border-color: var(--input-background-color-active);
	}

	&.smaller {
		.icon i {
			font-size: 60px !important;
		}
	}
}

.v-upload.uploading {
	display: flex;
	flex-direction: column;

	.dragging & ol {
		transition: border-color var(--slow) var(--transition);
		border-color: var(--input-background-color-active);
	}

	.dropzone {
		background-color: var(--input-background-color-alt);
		border-top-left-radius: var(--border-radius);
		border-top-right-radius: var(--border-radius);
		padding: 8px 12px;
		padding-right: 50px;
		border: var(--input-border-width) solid var(--input-border-color);
		border-bottom: none;
		color: var(--heading-text-color);
		flex-shrink: 0;

		.info {
			overflow: hidden;
		}

		.file-info {
			color: var(--note-text-color);
		}

		.icon {
			width: 48px;
			height: 48px;
			display: flex;
			justify-content: center;
			align-items: center;
			border: 2px solid var(--input-border-color);
			border-radius: 50%;
		}

		.buttons {
			& > * {
				color: var(--input-icon-color);

				&:hover {
					color: var(--heading-text-color);
				}
			}
		}
	}

	ol {
		flex-grow: 1;
		border: var(--input-border-width) dashed var(--input-border-color);
		border-top: 0;
		border-bottom-left-radius: var(--border-radius);
		border-bottom-right-radius: var(--border-radius);
		padding: 0 12px;
		list-style: none;
		overflow: auto;
		-webkit-overflow-scrolling: touch;
	}

	.dropzone,
	li {
		display: flex;
		align-items: center;
		height: 70px;

		.icon {
			margin-right: 5px;
		}
	}

	li {
		padding: 10px 0;
		background-color: var(--input-background-color);

		&:not(:last-of-type) {
			border-bottom: 2px solid var(--input-background-color-alt);
		}

		.file-info {
			color: var(--note-text-color);
		}
	}
}

.v-upload.disabled .buttons {
	pointer-events: none;
}

.dragging input.drop {
	pointer-events: auto;
}

.list-item {
	display: inline-block;

	.info {
		overflow: hidden;
	}
}

.list-enter-active,
.list-leave-active {
	transition: all var(--slow) var(--transition);
}

.list-enter,
.list-leave-to {
	opacity: 0;
}

.list-move {
	transition: all var(--slow) var(--transition);
}
</style>
