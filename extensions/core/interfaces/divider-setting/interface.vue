<template>
    <v-select
    :class="width"
    :value="value"
    :disabled="readonly"
    :id="name"
    :options="choices"
    :placeholder="options.placeholder"
    @input="$emit('input', $event)" />
</template>

<script>
import mixin from "../../../mixins/interface";
export default {
  mixins: [mixin],
  computed: {
    choices() {
      return ["fullwidth", "description", "title", "small", "big"];
    }
  }
};
</script>

<style lang="scss" scoped>
</style>


