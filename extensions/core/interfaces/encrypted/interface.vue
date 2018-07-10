<template>
  <div class="interface-encrypted">
    <v-input
      :class="width"
      :type="inputType"
      :value="value"
      :icon-right="lockIcon"
      :icon-right-color="iconColor"
      @input="$emit('input', $event)" />
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
    mixins: [mixin],
    data() {
        return {
            originalValue: this.value || ""
        };
    },
    computed: {
        width() {
            if (this.options.width !== "auto") return this.options.width;

            const length = this.length;

            if (!length) return "normal";

            if (length <= 7) return "x-small";
            else if (length > 7 && length <= 25) return "small";
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
