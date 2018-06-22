<template>
  <v-input
    type="text"
    :class="width"
    :value="value"
    :readonly="readonly"
    :placeholder="options.placeholder"
    :maxlength="length"
    :id="name"
    @input="updateValue"
  />
</template>

<script>
import slug from 'slugify';

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
    mirror() {
      const { mirroredField } = this.options;

      return (this.$store.state.edits.values &&
        this.$store.state.edits.values[mirroredField]) || '';
    },
  },
  watch: {
    mirror() {
      this.updateValue(this.mirror);
    },
  },
  methods: {
    updateValue(value) {
      this.$emit('input', slug(value, {
        lower: this.options.forceLowercase,
      }));
    },
  },
}
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
