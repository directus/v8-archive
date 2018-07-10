<template>
  <v-input
    type="number"
    class="interface-file-size"
    :readonly="readonly"
    :placeholder="options.placeholder"
    :value="value"
    @input="$emit('input', $event)"
  />
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin]
};
</script>

<style lang="scss" scoped>
.interface-file-size {
  max-width: var(--width-small);
}
</style>
