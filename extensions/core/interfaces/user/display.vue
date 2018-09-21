<template>
  <div>
    <v-avatar
      v-if="options.display !== 'name'"
      class="display-user"
      :size="28"
      :src="src"
      :alt="displayValue"
      v-tooltip="displayValue"
      color="light-gray"></v-avatar>
      <span v-if="options.display !== 'avatar'" class="label">{{displayValue}}</span>
    </div>
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
    }
  }
};
</script>

<style lang="scss" scoped>
.display-user {
  width: max-content;
  display: inline-block;
  vertical-align: middle;
  margin-right: 4px;
}
.label {
  display: inline-block;
}
</style>
