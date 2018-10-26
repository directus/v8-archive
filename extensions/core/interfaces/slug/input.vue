<template>
  <v-input
    type="text"
    class="slug"
    :value="value"
    :readonly="readonly"
    :placeholder="options.placeholder"
    :maxlength="length"
    :id="name"
    @input="updateValue"></v-input>
</template>

<script>
import slug from "slugify";

import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  computed: {
    mirror() {
      const { mirroredField } = this.options;

      return this.values[mirroredField];
    }
  },
  watch: {
    mirror() {
      this.updateValue(this.mirror);
    }
  },
  methods: {
    updateValue(value) {
      this.$emit(
        "input",
        slug(value, {
          lower: this.options.forceLowercase,
          remove: /[$*_+~.()'"!:@]/g
        })
      );
    }
  }
};
</script>

<style lang="scss" scoped>
.slug {
  max-width: var(--width-medium);
}
</style>
