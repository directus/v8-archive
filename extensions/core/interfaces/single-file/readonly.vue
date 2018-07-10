<template>
  <div class="readonly-single-file no-wrap">
    <img
      v-if="value && value.storage && value.storage.full_url && !error"
      @error="handleImageError"
      :src="value.storage.full_url" />
    <i v-else-if="error" class="material-icons">broken_image</i>
    <span v-else>--</span>
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  data() {
    return {
      error: null
    };
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
