<template>
<div>
  <i
    class="material-icons readonly-toggle-icon"
    :style="{color: `var(--${colorChange})`}">{{ icon }}</i>
    <span :style="{color: `var(--${colorChange})`}">{{ textChange }}</span>
    </div>
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
      return this.value ? this.options.colorActive : this.options.colorInactive;
    },
    textChange() {
      return this.value ? this.options.textActive : this.options.textInactive;
    }
  }
};
</script>

<style lang="scss" scoped>
div > span {
  text-transform: uppercase;
  line-height: 1.18;
  font-weight: 700;
  font-size: 0.85em;
}
</style>
