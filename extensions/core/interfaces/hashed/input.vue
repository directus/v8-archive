<template>
  <div class="interface-encrypted">
    <v-input
      v-model="newValue"
      :placeholder="options.showHash ? originalValue : $t('interfaces-hashed-secured')"
      :type="inputType"
      :icon-right="lockIcon"
      :icon-right-color="iconColor"></v-input>
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  data() {
    return {
      originalValue: this.value || "",
      newValue: ""
    };
  },
  watch: {
    newValue(val) {
      this.$emit("input", val);
    }
  },
  computed: {
    valueChanged() {
      return this.value !== this.originalValue;
    },
    inputType() {
      return this.options.hide ? "password" : "text";
    },
    lockIcon() {
      return this.valueChanged ? "lock_open" : "lock_outline";
    },
    iconColor() {
      return this.valueChanged ? "warning" : "accent";
    }
  }
};
</script>

<style lang="scss" scoped>
.v-input {
  width: 100%;
  max-width: var(--width-normal);
}
</style>
