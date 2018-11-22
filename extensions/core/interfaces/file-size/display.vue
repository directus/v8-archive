<template>
  <div class="no-wrap" v-if="options.format">
    {{ formatSize(value, options.decimal) }}
  </div>
  <div class="no-wrap" v-else>{{ value }}</div>
</template>

<script>
import mixin from "../../../mixins/interface";
import formatSize from "./format-size";

export default {
  mixins: [mixin],
  methods: {
    formatSize
  }
};
</script>
