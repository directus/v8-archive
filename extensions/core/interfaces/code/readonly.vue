<template>
  <div class="no-wrap"><i class="material-icons" :title="hover" :class="empty">code</i></div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  data() {
    return {
      empty: "empty",
      avail_types: {
        "text/javascript": "Javascript",
        "application/json": "JSON",
        "text/x-vue": "Vue",
        "application/x-httpd-php": "PHP"
      }
    }
  },
  computed: {
    hover() {
      let hoverText = (this.value)? this.value.length : 0;
      let plural = (hoverText == 1)? "" : "s";
      let language = this.avail_types[this.options.mode];

      this.empty = (hoverText > 0)? "" : "empty";

      hoverText = `${hoverText} character${plural} of ${language}`;
      return hoverText;
    }
  }
};
</script>

<style lang="scss" scoped>
  i.material-icons {
    cursor: help;
    &.empty {
      color: var(--lighter-gray);
    }
  }
</style>
