<template>
  <div class="no-wrap">{{ displayValue }}</div>
</template>

<script>
import mixin from "../../../mixins/interface";
import allLanguages from "./languages.json";

export default {
  mixins: [mixin],
  computed: {
    displayValue() {
      if (!this.value) return null;
      return allLanguages[this.value];
    }
  }
};
</script>
