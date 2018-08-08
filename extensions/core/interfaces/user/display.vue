<template>
  <v-avatar
    class="display-user"
    :size="24"
    :src="src"
    :alt="displayValue"
    v-tooltip="displayValue"
    color="light-gray" />
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  computed: {
    displayValue() {
      return this.$helpers.micromustache.render(
        this.options.template,
        this.value
      );
    },
    src() {
      return (
        this.value.avatar &&
        this.value.avatar.data &&
        this.value.avatar.data.full_url
      );
    }
  }
};
</script>

<style lang="scss" scoped>
.display-user {
  width: max-content;
}
</style>
