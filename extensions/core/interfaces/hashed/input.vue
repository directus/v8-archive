<template>
  <div class="interface-encrypted">
    <v-input
      v-model="newValue"
      :placeholder="options.showHash ? originalValue : $t('interfaces-hashed-hashed')"
      :class="width"
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
    width() {
      if (this.options.width !== "auto") return this.options.width;

      const length = this.length;

      if (!length) return "normal";

      if (length <= 25) return "small";
      else return "medium";
    },
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
.x-small {
  max-width: var(--width-x-small);
}

.small {
  max-width: var(--width-small);
}

.medium {
  max-width: var(--width-normal);
}
</style>
