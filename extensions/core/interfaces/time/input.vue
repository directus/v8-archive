<template>
  <v-input
    type="time"
    class="input"
    :readonly="readonly"
    @input="$emit('input', $event)"
    :value="value"></v-input>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin]
};
</script>

<style lang="scss" scoped>
.input {
  max-width: var(--width-small);
}
</style>
