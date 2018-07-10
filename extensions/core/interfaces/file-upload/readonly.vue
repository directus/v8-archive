<template>
  <div class="readonly-file-upload">
    <img
      v-if="value && value.url && !error"
      @error="handleImageError"
      :src="value.full_url" />
    <i v-else-if="error" class="material-icons">broken_image</i>
    <span
      v-else-if="!value">--</span>
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
