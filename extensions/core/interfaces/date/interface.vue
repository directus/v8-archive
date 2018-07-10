<template>
  <v-input
    type="date"
    class="interface-date"
    :id="name"
    :name="name"
    :min="options.min"
    :max="options.max"
    :readonly="readonly"
    :value="value"
    @input="$emit('input', $event)"
  />
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
    name: "interface-date",
    mixins: [mixin]
};
</script>

<style lang="scss" scoped>
.interface-date {
    max-width: var(--width-small);
}
</style>
