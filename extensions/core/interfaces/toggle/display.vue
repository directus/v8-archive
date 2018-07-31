<template>
  <i :class="['material-icons', 'no-wrap', icon]">{{ icon }}</i>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  computed: {
    icon() {
      if (this.options.showAsCheckbox) {
        return this.value ? "check_box" : "check_box_outline_blank";
      }

      return this.value ? "check" : "close";
    }
  }
};
</script>

<style lang="scss" scoped>
.close {
  opacity: 0.3;
}
</style>
