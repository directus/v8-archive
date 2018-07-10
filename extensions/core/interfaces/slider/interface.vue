<template>
  <v-slider
    :disabled="readonly"
    :class="options.width"
    :max="options.maximum"
    :min="options.minimum"
    :step="options.step"
    :unit="options.unit"
    :value="value"
    @input="$emit('input', $event)" />
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
    name: "interface-slider",
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
