<template>
  <v-input
    class="small"
    :readonly="true"
    :value="value"
    :icon-left="options.iconLeft"
    :icon-right="options.iconRight"></v-input>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  name: "interface-activity-icon",
  mixins: [mixin]
};
</script>

<style lang="scss" scoped>
.small {
  width: 100%;
  max-width: var(--width-normal);
}
</style>
