<template>
  <div>
    <div v-if="isImage" class="image">
      <img :src="url">
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
      <span class="original">
        <a :href="url" target="_blank"><i class="material-icons">link</i>{{url}}</a>
      </span>
    </div>
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
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
    max-height: 400px;
    max-width: 100%;
    display: block;
  }
  video {
    margin: 0 auto;
    max-height: 400px;
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
  a {
    transition: var(--fast) var(--transition);
    text-decoration: none;
    color: var(--gray);
    &:hover {
      color: var(--darker-gray);
    }
  }
  span {
    margin-right: 10px;
    vertical-align: middle;
  }
  .original {
    display: inline-block;
    margin-top: 10px;
    i {
      margin-right: 6px;
    }
  }
}
</style>
