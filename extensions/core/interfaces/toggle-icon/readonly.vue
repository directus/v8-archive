<template>
  <i
    class="material-icons readonly-toggle-icon"
    :style="{color: `var(--${colorChange})`}">{{ icon }}</i>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  name: "readonly-toggle-icon",
  mixins: [mixin],
  computed: {
    icon() {
      return this.value ? this.options.iconActive : this.options.iconInactive;
    },
    colorChange() {
      return this.value ? this.options.colorA : this.options.colorB;
    }
  }
};
</script>
