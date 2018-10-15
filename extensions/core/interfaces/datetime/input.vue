<template>
  <v-input
    type="datetime-local"
    class="interface-datetime"
    :id="name"
    :name="name"
    :min="options.min"
    :max="options.max"
    :readonly="readonly"
    :value="ISO"
    :icon-left="options.iconLeft"
    :icon-right="options.iconRight"
    @input="updateValue"></v-input>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  computed: {
    date() {
      if (!this.value) return;
      return new Date(this.value);
    },
    ISO() {
      if (!this.value) return;
      return `${this.date.getFullYear()}-${String(
        this.date.getMonth()+1
      ).padStart(2, "0")}-${String(this.date.getDate()).padStart(
        2,
        "0"
      )}T${String(this.date.getHours()).padStart(2, "0")}:${String(
        this.date.getMinutes()
      ).padStart(2, "0")}:00`;
    }
  },
  methods: {
    updateValue(value) {
      this.$emit("input", this.$helpers.date.dateToSql(new Date(value)));
    }
  }
};
</script>

<style lang="scss" scoped>
.interface-datetime {
  max-width: var(--width-medium);
}
</style>
