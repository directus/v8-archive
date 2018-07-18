<template>
  <i
    v-tooltip="tooltipCopy"
    :class="{ empty }"
    class="material-icons" >code</i>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  name: "readonly-code",
  mixins: [mixin],
  computed: {
    lineCount() {
      if (!this.value) return 0;
      return this.value.split(/\r\n|\r|\n/).length;
    },
    availableTypes() {
      return {
        "text/javascript": "JavaScript",
        "application/json": "JSON",
        "text/x-vue": "Vue",
        "application/x-httpd-php": "PHP"
      };
    },
    language() {
      return this.availableTypes[this.options.language];
    },
    tooltipCopy() {
      return this.$tc("interfaces-code-loc", this.lineCount, {
        count: this.lineCount,
        lang: this.language
      });
    },
    empty() {
      return this.lineCount === 0;
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
