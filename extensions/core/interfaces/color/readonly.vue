<template>
  <div
    v-if="options.formatValue"
    class="swatch"
    :style="`background-color: ${displayValue}`"
  ></div>
  <div v-else>{{ displayValue }}</div>
</template>

<script>
import meta from './meta.json';
import mixin from '../../../mixins/interface';
import Color from 'color';

export default {
  mixins: [mixin],
  computed: {
    displayValue() {
      let value = this.options.output === 'hex' ?
        this.value : (
          Array.isArray(this.value) ?
          this.value :
          this.value.split(',')
        );

      if (this.options.formatValue === false) {
        if (Boolean(this.value) === false) {
          return '';
        }

        if (this.options.output === 'hex') {
          return this.value;
        }

        return this.value.join(', ');
      }

      if (this.options.output === 'hex') {
        return Color(this.value).rgb().string();
      }

      try {
        return Color[this.options.output](this.value).rgb().string();
      } catch (err) {
        return null;
      }
    },
  },
};
</script>

<style scoped>
.swatch {
  width: 20px;
  height: 20px;
  border-radius: 100%;
}
</style>
