<template>
  <v-input
    type="datetime-local"
    class="interface-datetime"
    :id="name"
    :name="name"
    :min="options.min"
    :max="options.max"
    :readonly="readonly"
    :value="formattedValue"
    :icon-left="options.iconLeft"
    :icon-right="options.iconRight"
    @input="updateValue"></v-input>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  computed: {
    formattedValue() {
      if (!this.value) return null;

      if (this.value.includes("T") && this.options.utc) {
        return this.value.substring(0, 16);
      }

      return this.toDatetimeLocal(new Date(this.value));
    }
  },
  methods: {
    updateValue(value) {
      if (!value) return;

      if (this.options.utc) {
        const timezoneOffset = new Date(value).getTimezoneOffset();
        const offsetHours = this.ten(Math.abs(timezoneOffset / 60));
        const offsetMinutes = this.ten(Math.abs(timezoneOffset % 60));

        return this.$emit(
          "input",
          value +
            (timezoneOffset < 0 ? "-" : "+") +
            offsetHours +
            ":" +
            offsetMinutes
        );
      }

      return this.$emit("input", value);
    },
    toDatetimeLocal(date) {
      const yyyy = date.getFullYear();
      const mm = this.ten(date.getMonth() + 1);
      const dd = this.ten(date.getDate());
      const hh = this.ten(date.getHours());
      const ii = this.ten(date.getMinutes());
      const ss = this.ten(date.getSeconds());
      return `${yyyy}-${mm}-${dd}T${hh}:${ii}:${ss}`;
    },
    ten(num) {
      return String(num).padStart(2, 0);
    }
  }
};
</script>

<style lang="scss" scoped>
.interface-datetime {
  max-width: var(--width-medium);
}
</style>
