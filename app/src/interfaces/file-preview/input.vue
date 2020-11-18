<template>
	<v-notice v-if="hasAllRequiredFields === false">
		File preview can only be used on directus_files.
	</v-notice>
	<div v-else>
		<div v-if="isImage" class="image">
			<img
				v-if="!imgError"
				id="image"
				:key="image.hash"
				:src="url"
				@error="imgError = true"
			/>
			<div v-if="imgError" class="broken-image">
				<v-icon name="broken_image" />
			</div>
			<button
				v-if="
					canBeEdited &&
						!imgError &&
						!editMode &&
						isImage &&
						options.edit.includes('image_editor')
				"
				type="button"
				title="Edit image"
				class="image-edit-start"
				@click="initImageEdit()"
			>
				<v-icon name="crop_rotate" />
			</button>
		</div>

		<div v-else-if="isVideo" class="video">
			<video controls>
				<source :src="url" :type="values.type" />
				I'm sorry; your browser doesn't support HTML5 video in this format.
			</video>
		</div>
		<div v-else-if="isAudio" class="audio">
			<audio controls>
				<source :src="url" :type="values.type" />
				I'm sorry; your browser doesn't support HTML5 audio in this format.
			</audio>
		</div>
		<div v-else-if="isYouTube" class="embed">
			<iframe
				width="620"
				height="349"
				:src="'https://www.youtube.com/embed/' + values.embed"
				frameborder="0"
				allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
				allowfullscreen
			></iframe>
		</div>
		<div v-else-if="isVimeo" class="embed">
			<iframe
				width="620"
				height="349"
				:src="
					'https://player.vimeo.com/video/' +
						values.embed +
						'?color=039be5&title=0&byline=0&portrait=0'
				"
				frameborder="0"
				webkitallowfullscreen
				mozallowfullscreen
				allowfullscreen
			></iframe>
		</div>
		<div v-else class="file">{{ fileType }}</div>
		<div class="toolbar">
			<!-- Default Toolbar -->
			<div v-if="canBeEdited === false || !editMode" class="original">
				<a class="file-link" :href="url" target="_blank">
					<v-icon name="link" />
					{{ url }}
				</a>
			</div>

			<!-- Image Edit Toolbar -->
			<ul v-if="canBeEdited && editMode" class="image-edit">
				<li>
					<div class="image-aspect-ratio">
						<v-icon name="image_aspect_ratio" />
						<span>{{ image.cropRatioOptions[image.cropRatio] }}</span>
						<v-icon name="arrow_drop_down" />
						<select v-model="image.cropRatio" title="Select aspect ratio">
							<option
								v-for="(option, value) in image.cropRatioOptions"
								:key="value"
								:value="value"
							>
								{{ option }}
							</option>
						</select>
					</div>
				</li>
				<li>
					<button type="button" title="Discard changes" @click="cancelImageEdit()">
						<v-icon name="not_interested" />
					</button>
					<button type="button" title="Save changes" @click="saveImage()">
						<v-icon name="check_circle" />
					</button>
				</li>
				<li>
					<button type="button" title="Flip horizontally" @click="flipImage()">
						<v-icon name="flip" />
					</button>
					<button type="button" title="Rotate counter-clockwise" @click="rotateImage()">
						<v-icon name="rotate_90_degrees_ccw" />
					</button>
				</li>
			</ul>
		</div>
	</div>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';
import Cropper from 'cropperjs';
import 'cropperjs/dist/cropper.min.css';
import shortid from 'shortid';
import { has } from 'lodash';

export default {
	mixins: [mixin],
	data() {
		return {
			editMode: null,
			imgError: false,
			image: {
				hash: shortid.generate(), //To prevent the cacheing issue of image
				cropper: null, //cropper instance
				cropRatio: 'free', // Aspect ratio set by cropper
				cropRatioOptions: {
					free: 'Free',
					original: 'Original',
					'1:1': 'Square',
					'16:9': '16:9',
					'4:3': '4:3',
					'3:2': '3:2'
				},
				initOptions: {
					background: false,
					viewMode: 0,
					autoCropArea: 1,
					zoomable: false
				}
			}
		};
	},
	computed: {
		hasAllRequiredFields() {
			const requiredFields = ['type', 'filesize', 'data', 'id'];
			let valid = true;
			requiredFields.forEach(field => {
				if (has(this.values, field) === false) {
					valid = false;
				}
			});
			return valid;
		},
		canBeEdited() {
			return this.values?.filesize <= 1000000 && this.isImage;
		},
		isImage() {
			switch (this.values?.type) {
				case 'image/jpeg':
				case 'image/gif':
				case 'image/png':
				case 'image/svg+xml':
				case 'image/webp':
				case 'image/bmp':
					return true;
			}
			return false;
		},
		isVideo() {
			switch (this.values?.type) {
				case 'video/mp4':
				case 'video/webm':
				case 'video/ogg':
					return true;
			}
			return false;
		},
		isAudio() {
			switch (this.values?.type) {
				case 'audio/mpeg':
				case 'audio/ogg':
				case 'audio/wav':
					return true;
			}
			return false;
		},
		isYouTube() {
			return this.values?.type === 'embed/youtube';
		},
		isVimeo() {
			return this.values?.type === 'embed/vimeo';
		},
		fileType() {
			return this.values?.type.split('/')[1];
		},
		url() {
			return this.values?.data.asset_url;
		}
	},
	watch: {
		'image.cropRatio'(newValue) {
			this.setAspectRatio(newValue);
		}
	},
	methods: {
		initImageEdit() {
			this.editMode = 'image';
			this.image.show = false;
			const image = document.getElementById('image');
			this.image.cropper = new Cropper(image, {
				...this.image.initOptions
			});

			window.addEventListener('keydown', this.escapeEditImage);
		},

		escapeEditImage(event) {
			if (this.editMode == 'image' && event.key == 'Escape') {
				this.cancelImageEdit();
				window.removeEventListener('keydown', this.escapeEditImage);
			}
		},

		cancelImageEdit() {
			this.editMode = null;
			this.image.cropRatio = 'free';
			this.image.cropper.destroy();
		},

		setAspectRatio(value) {
			let aspectRatio;
			switch (value) {
				case 'free': {
					aspectRatio = 'free';
					break;
				}
				case 'original': {
					aspectRatio = this.image.cropper.getImageData().aspectRatio;
					break;
				}
				default: {
					const values = value.split(':');
					aspectRatio = values[0] / values[1];
					break;
				}
			}
			this.image.cropper.setAspectRatio(aspectRatio);
		},

		flipImage() {
			this.image.cropper.scale(-this.image.cropper.getData().scaleX, 1);
		},

		rotateImage() {
			this.image.cropper.rotate(-90);
		},

		async saveImage() {
			//Running the rabbit
			const isSaving = this.$helpers.shortid.generate();
			this.$store.dispatch('loadingStart', {
				id: isSaving
			});

			//Converting an image to base64
			const imageBase64 = this.image.cropper
				.getCroppedCanvas({
					imageSmoothingQuality: 'high'
				})
				.toDataURL(this.values?.type);

			try {
				await this.$api.api.patch(`/files/${this.values?.id}`, {
					data: imageBase64
				});

				this.$events.emit('success', {
					notify: 'Image updated.'
				});

				this.image.hash = shortid.generate();
				this.editMode = null;
				this.image.cropRatio = 'free';
				this.image.cropper.destroy();
			} catch (err) {
				this.$events.emit('error', {
					notify: 'There was an error while saving the image',
					error: err
				});
			} finally {
				this.$store.dispatch('loadingFinished', isSaving);
			}
		}
	}
};
</script>

<style lang="scss" scoped>
.file,
.audio,
.video,
.image {
	position: relative;
	width: 100%;
	background-color: var(--black);
	text-align: center;
	border-radius: var(--border-radius);
	overflow: hidden;
	img {
		margin: 0 auto;
		max-height: 414px;
		max-width: 100%;
		display: block;
	}
	video {
		margin: 0 auto;
		max-height: 414px;
		max-width: 100%;
		display: block;
	}
	audio {
		margin: 0 auto;
		width: 100%;
		max-width: 100%;
		display: block;
	}
}
.image {
	&:hover {
		.image-edit-start {
			opacity: 1;
		}
	}
}
.audio,
.file {
	padding: 80px 40px;
	font-size: 3em;
	text-transform: uppercase;
	font-weight: 300;
	color: var(--blue-grey-200);
}
.toolbar {
	margin-top: 10px;
	.original {
		display: flex;
		align-items: flex-start;
		justify-content: space-between;
	}
}
.file-link {
	transition: var(--fast) var(--transition);
	text-decoration: none;
	color: var(--input-placeholder-color);
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
	&:hover {
		color: var(--input-text-color);
		i {
			color: var(--input-text-color);
		}
	}
	i {
		margin-right: 4px;
		color: var(--input-placeholder-color);
	}
	span {
		margin-right: 10px;
		vertical-align: middle;
	}
}
.image-edit-start {
	position: absolute;
	bottom: 8px;
	right: 8px;
	z-index: 1;
	width: 40px;
	height: 40px;
	background-color: var(--page-background-color);
	border-radius: var(--border-radius);
	padding: 8px;
	opacity: 0;
	transition: all var(--fast) var(--transition);
	&:hover {
		i {
			color: var(--warning);
		}
	}
	i {
		color: var(--input-text-color);
	}
}

.image-edit {
	display: flex;
	list-style: none;
	margin: 0;
	padding: 0;
	> li {
		flex: 0 0 33.33%;
		text-align: center;
		button {
			color: var(--blue-grey-600);
			+ button {
				margin-left: 10px;
			}
			&:hover {
				color: var(--blue-grey-900);
			}
		}
		&:first-child {
			text-align: left;
		}
		&:last-child {
			text-align: right;
		}
	}
}

.image-aspect-ratio {
	position: relative;
	display: inline-flex;
	align-items: center;
	span {
		margin-left: 8px;
	}
	select {
		cursor: pointer;
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		opacity: 0;
	}
}
</style>

<style lang="scss">
.image {
	.cropper-point {
		background: #fff;
		height: 10px;
		width: 10px;
		border-radius: 50%;
		opacity: 1;
		&.point-n {
			top: -5px;
			margin-left: -5px;
		}
		&.point-ne {
			right: -5px;
			top: -5px;
		}
		&.point-e {
			margin-top: -5px;
			right: -5px;
		}
		&.point-se {
			right: -5px;
			bottom: -5px;
		}
		&.point-s {
			bottom: -5px;
			margin-left: -5px;
		}
		&.point-sw {
			left: -5px;
			bottom: -5px;
		}
		&.point-w {
			margin-top: -5px;
			left: -5px;
		}
		&.point-nw {
			left: -5px;
			top: -5px;
		}
	}
	.cropper-dashed {
		border-style: solid;
		border-color: #fff;
		opacity: 0.4;
		box-shadow: 0 0px 0px 1px rgba(0, 0, 0, 0.3);
	}
	.cropper-line {
		background-color: #000;
		opacity: 0.05;
	}
}
.broken-image {
	width: 100%;
	padding: 70px 0;
	display: flex;
	justify-content: center;
	align-items: center;

	i {
		color: var(--blue-grey-300);
		font-size: 48px;
	}
}
</style>
