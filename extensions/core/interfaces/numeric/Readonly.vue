<template>
  <span v-if="options.localized">{{ $n(value) }}</span>
  <span v-else>{{value}}</span>
</template>

<script>
import mixin from '../../../mixins/interface';

export default {
  mixins: [mixin],
};
</script>
