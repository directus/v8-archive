<template>
  <div>
    <div v-if="isImage" class="image">
      <img id="image" :src="vUrl">
    </div>
    <div v-else-if="isVideo" class="video">
      <video controls>
        <source :src="url" :type="values.type">
        I'm sorry; your browser doesn't support HTML5 video in this format.
      </video>
    </div>
    <div v-else-if="isAudio" class="audio">
      <audio controls>
        <source :src="url" :type="values.type">
        I'm sorry; your browser doesn't support HTML5 audio in this format.
      </audio>
    </div>
    <div v-else class="file">
      {{ fileType }}
    </div>
    <div class="toolbar">

      <!-- Default Toolbar -->
      <div v-if="!editMode" class="original">
        <a 
          class="file-link" 
          :href="url" 
          target="_blank">
          <i class="material-icons">link</i>
          {{url}}
        </a>
        <button 
          v-if="isImage && options.edit.includes('image_editor')" 
          type="button"
          title="Edit image"
          class="image-edit-start" 
          @click="initImageEdit()">
          <i class="material-icons">crop_rotate</i>
        </button>
      </div>

      <!-- Image Edit Toolbar -->
      <ul v-if="editMode" class="image-edit">
        <li>
          <div class="image-aspect-ratio">
            <i class="material-icons">image_aspect_ratio</i>
            <span>{{image.cropRatioOptions[image.cropRatio]}}</span>
            <i class="material-icons">arrow_drop_down</i>
            <select v-model="image.cropRatio" title="Select aspect ratio">
              <option
                v-for="(option,value) in image.cropRatioOptions"
                :key="value"
                :value="value">
                {{option}}
              </option>
            </select>
          </div>
        </li>
        <li>
          <button type="button" title="Discard changes" @click="cancelImageEdit()">
            <i class="material-icons">not_interested</i>
          </button>
          <button type="button" title="Save changes" @click="saveImage()">
            <i class="material-icons">check_circle</i>
          </button>
        </li>
        <li>
          <button type="button" title="Flip horizontally" @click="flipImage()">
            <i class="material-icons">flip</i>
          </button>
          <button type="button" title="Rotate counter-clockwise" @click="rotateImage()">
            <i class="material-icons">rotate_90_degrees_ccw</i>
          </button>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";
import Cropper from "cropperjs";
import "cropperjs/dist/cropper.min.css";

export default {
  mixins: [mixin],
  data() {
    return {
      editMode: null,
      image: {
        version: 0, //To prevent the cacheing issue of image
        cropper: null, //cropper instance
        cropRatio: "free", // Aspect ratio set by cropper
        cropRatioOptions: {
          free: "Free",
          original: "Original",
          "1:1": "Square",
          "16:9": "16:9",
          "4:3": "4:3",
          "3:2": "3:2"
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
  watch: {
    "image.cropRatio"(newValue) {
      this.setAspectRatio(newValue);
    }
  },
  computed: {
    isImage() {
      switch (this.values.type) {
        case "image/jpeg":
        case "image/gif":
        case "image/png":
        case "image/svg+xml":
        case "image/webp":
        case "image/bmp":
          return true;
      }
      return false;
    },
    isVideo() {
      switch (this.values.type) {
        case "video/mp4":
        case "video/webm":
        case "video/ogg":
          return true;
      }
      return false;
    },
    isAudio() {
      switch (this.values.type) {
        case "audio/mpeg":
        case "audio/ogg":
        case "audio/wav":
          return true;
      }
      return false;
    },
    fileType() {
      return this.values.type.split("/")[1];
    },
    url() {
      return this.values.data.full_url;
    },
    vUrl() {
      /**
       * Timestamp fetches the latest image from server
       * Version helps to refresh the image after crop
       */
      return `${this.values.data.full_url}?v=${
        this.image.version
      }&timestamp=${new Date().getTime()}`;
    }
  },
  methods: {
    initImageEdit() {
      let _this = this;
      this.editMode = "image";
      this.image.show = false;
      let _image = document.getElementById("image");
      this.image.cropper = new Cropper(_image, {
        ...this.image.initOptions
      });
      window.addEventListener("keydown", function escapeEditImage(e) {
        if (_this.editMode == "image" && e.key == "Escape") {
          _this.cancelImageEdit();
          window.removeEventListener("keydown", escapeEditImage);
        }
      });
    },

    cancelImageEdit() {
      this.editMode = null;
      this.image.cropRatio = "free";
      this.image.cropper.destroy();
    },

    setAspectRatio(value) {
      let _aspectRatio;
      switch (value) {
        case "free":
          _aspectRatio = "free";
          break;
        case "original":
          _aspectRatio = this.image.cropper.getImageData().aspectRatio;
          break;
        default:
          let _values = value.split(":");
          _aspectRatio = _values[0] / _values[1];
          break;
      }
      this.image.cropper.setAspectRatio(_aspectRatio);
    },

    flipImage() {
      this.image.cropper.scale(-this.image.cropper.getData().scaleX, 1);
    },

    rotateImage() {
      this.image.cropper.rotate(-90);
      //TODO: Fix the image rotation issue
      /**
       * White rotating the image, the sides are getting cut of
       * due to limitations of the cropper.js plugin
       */
    },

    saveImage() {
      //Running the rabbit
      let isSaving = this.$helpers.shortid.generate();
      this.$store.dispatch("loadingStart", {
        id: isSaving
      });

      //Converting an image to base64
      let _imageBase64 = this.image.cropper
        .getCroppedCanvas({
          imageSmoothingQuality: "high"
        })
        .toDataURL(this.values.type);

      //Saving the image via API
      this.$api
        .patch(`/files/${this.values.id}`, {
          data: _imageBase64
        })
        .then(res => {
          this.$events.emit("success", {
            notify: "Image updated."
          });
        })
        .catch(err => {
          this.$events.emit("error", {
            notify: "There was an error while saving the image",
            error: err
          });
        })
        .then(() => {
          var _this = this;
          this.image.version++;
          /**
           * This will wait for new cropped image to load from server
           * & then destroy the cropper instance
           * This prevents flickering between old and new image
           */
          const img = new Image();
          img.src = this.vUrl;
          img.onload = function() {
            _this.$store.dispatch("loadingFinished", isSaving);
            _this.cancelImageEdit();
          };
        });
    }
  }
};
</script>

<style lang="scss" scoped>
.file,
.audio,
.video,
.image {
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
.audio,
.file {
  padding: 80px 40px;
  font-size: 3em;
  text-transform: uppercase;
  font-weight: 300;
  color: var(--lighter-gray);
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
  color: var(--gray);
  &:hover {
    color: var(--darker-gray);
  }
  i {
    margin-right: 6px;
    color: var(--gray);
  }
  span {
    margin-right: 10px;
    vertical-align: middle;
  }
}
.image-edit-start {
  margin-left: 10px;
  i {
    color: var(--gray);
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
      color: var(--dark-gray);
      + button {
        margin-left: 10px;
      }
      &:hover {
        color: var(--darkest-gray);
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
</style>

