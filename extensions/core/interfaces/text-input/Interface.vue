<template>
  <v-input
    type="text"
    :class="width"
    :value="value || ''"
    :readonly="readonly"
    :placeholder="options.placeholder"
    :maxlength="+length"
    :id="name"
    :charactercount="options.showCharacterCount"
    @input="updateValue" />
</template>

<script>
import mixin from '../../../mixins/interface';

export default {
  mixins: [mixin],
  computed: {
    width() {
      if (this.options.width !== 'auto') return this.options.width;

      const length = this.length;

      if (!length) return 'normal';

      if (length <= 7) return 'x-small';
      else if (length > 7 && length <= 25) return 'small';
      else return 'medium';
    },
  },
  methods: {
    updateValue(rawValue) {
      let value = rawValue;

      if (this.options.trim) {
        value = value.trim();
      }

      this.$emit('input', value);
    },
  },
};
</script>

<style lang="scss" scoped>
.x-small {
  max-width: var(--width-x-small);
}

.small {
  max-width: var(--width-small);
}

.medium {
  max-width: var(--width-normal);
}
</style>
