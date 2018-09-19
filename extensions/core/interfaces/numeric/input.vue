<template>
  <v-input
    type="number"
    class="number"
    :readonly="readonly"
    :placeholder="options.placeholder"
    :value="value"
    :icon-left="options.iconLeft"
    :icon-right="options.iconRight"
    @input="$emit('input', Number($event))"></v-input>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin]
};
</script>

<style lang="scss" scoped>
.number {
  width: 100%;
  max-width: var(--width-normal);
}
</style>
