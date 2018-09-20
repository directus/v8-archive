<template>
  <i class="material-icons readonly-password" :style="{color: `var(--${colorChange})`}">{{ icon }}</i>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  name: "readonly-password",
  mixins: [mixin],
  computed: {
    icon() {
      return this.value ? "lock_outline" : "lock_open";
    },
    colorChange() {
      return this.value ? "dark-gray" : "warning";
    }
  }
};
</script>

<style lang="scss" scoped>

</style>
