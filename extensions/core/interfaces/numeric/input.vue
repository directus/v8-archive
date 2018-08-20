<template>
  <v-input
    type="number"
    :class="options.width"
    :readonly="readonly"
    :placeholder="options.placeholder"
    :value="value"
    :icon-left="options.iconLeft"
    :icon-right="options.iconRight"
    @input="$emit('input', Number($event))"/>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin]
};
</script>

<style lang="scss" scoped>
.small {
  max-width: var(--width-small);
}

.medium {
  max-width: var(--width-normal);
}

.large {
  max-width: var(--width-large);
}
</style>
