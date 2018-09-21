<template>
  <div v-if="userInfo">
    <v-avatar
      v-if="options.display !== 'name'"
      class="display-user"
      :size="28"
      :src="src"
      :alt="displayValue"
      v-tooltip="options.display === 'avatar' ? displayValue : null"
      color="light-gray"></v-avatar>
    <span v-if="options.display !== 'avatar'" class="label">{{displayValue}}</span>
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  computed: {
    userInfo() {
      if (!this.value) return null;

      if (typeof this.value === 'object') return this.value;
      return this.$store.state.users[this.value];
    },
    displayValue() {
      return this.$helpers.micromustache.render(
        this.options.template,
        this.userInfo
      );
    },
    src() {
      if (!this.userInfo.avatar) return null;
      return this.userInfo.avatar.data.thumbnails[0].url;
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
