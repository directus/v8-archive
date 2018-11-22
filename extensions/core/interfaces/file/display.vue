<template>
  <div class="readonly-single-file no-wrap">
    <img v-if="imageUrl && !error" @error="handleImageError" :src="imageUrl" />
    <i v-else-if="error" class="material-icons">broken_image</i>
    <span v-else-if="!value">--</span>
    <span
      v-else
      class="material-icons"
      v-tooltip.right="value && value.filename"
      >{{ icon }}</span
    >
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";
import getIcon from "./get-icon";

export default {
  mixins: [mixin],
  data() {
    return {
      error: null
    };
  },
  computed: {
    filetype() {
      if (!this.value) return null;
      return this.value.type;
    },
    isImage() {
      return this.filetype && this.filetype.startsWith("image");
    },
    icon() {
      return getIcon(this.filetype);
    },
    imageUrl() {
      if (!this.isImage) return null;

      return (
        this.value &&
        this.value.data &&
        this.value.data.thumbnails &&
        this.value.data.thumbnails[0] &&
        this.value.data.thumbnails[0].url
      );
    }
  },
  methods: {
    handleImageError(error) {
      this.error = error;
    }
  }
};
</script>

<style lang="scss" scoped>
img {
  width: 24px;
  height: 24px;
  object-fit: cover;
  border-radius: 2px;
  display: block;
}

.spinner {
  display: inline-block;
}

i {
  color: var(--lighter-gray);
}
</style>
