<template>
  <input
    type="number"
    :readonly="readonly"
    :value="value"
    @input="updateValue($event.target.value)"
    min="0"
  />
</template>

<script>
import meta from './meta.json';
import mixin from '../../../mixins/interface';

export default {
  mixins: [mixin],
  methods: {
    updateValue(value) {
      this.$emit('input', Number(value));
    },
  },
}
</script>
