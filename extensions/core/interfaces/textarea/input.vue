<template>
  <v-textarea
    class="textarea"
    :readonly="readonly"
    :id="name"
    :value="value"
    :placeholder="options.placeholder"
    :rows="+options.rows"
    @input="$emit('input', $event)"></v-textarea>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin]
};
</script>

<style lang="scss" scoped>
.textarea {
  width: 100%;
  max-width: var(--width-x-large);
}
</style>
