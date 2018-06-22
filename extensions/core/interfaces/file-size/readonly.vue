<template>
  <div v-if="options.format">{{ formatSize(value, options.decimal) }}</div>
  <div v-else>{{value}}</div>
</template>

<script>
import mixin from '../../../mixins/interface';
import formatSize from './format-size';

export default {
  mixins: [mixin],
  methods: {
    formatSize,
  },
};
</script>
