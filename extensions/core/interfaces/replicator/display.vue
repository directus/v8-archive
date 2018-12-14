<template>
  <span>{{ itemCount }}</span>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  name: "readonly-code",
  mixins: [mixin],
  computed: {
    parsedValue() {
      return JSON.parse(this.value);
    },
    itemCount() {
      return this.parsedValue.length;
    }
  }
};
</script>

<style lang="scss" scoped>
span {
  cursor: help;

  &.empty {
    color: var(--lighter-gray);
  }
}
</style>
