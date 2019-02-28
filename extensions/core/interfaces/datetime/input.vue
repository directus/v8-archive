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
    @input="updateValue"
  ></v-input>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  computed: {
    formattedValue() {
      if (!this.value) return null;
      return this.toDateTimeFormat(this.value);
    }
  },
  created() {
    if (this.options.defaultToCurrentDatetime && !this.value) {
      //The default datetime-local accepts the value without milliseconds
      //The ISO String has milliseconds sepreated by '.'
      this.$emit("input", new Date().toISOString().split(".")[0]);
    }
  },
  methods: {
    updateValue(value) {
      //The datetime-local field does not emit change event untill full value is specified
      //So if user keeps value 2019-02-28 --:--:--:-- it will be saved as NULL
      if (value) {
        this.$emit("input", this.toDirectusFormat(value));
      } else {
        this.$emit("input", null);
      }
    },
    toDirectusFormat(value) {
      if (!value) return null;
      //The diffrence between Directus format and Value emmited by datetime-local field
      //Is only a seperator " " & "T" respectively.
      //The problem with date-fns is it accepts the year range inbeteen 1900-2099
      //So when use starts writing a year, suppose "2019", the year would be "0002" at first key stroke
      //This breaks date-fns function and returns random year from the range above.
      return value.trim().replace("T", " ");
    },
    toDateTimeFormat(value) {
      if (!value) return null;
      return value.trim().replace(" ", "T");
    }
  }
};
</script>

<style lang="scss" scoped>
.interface-datetime {
  max-width: var(--width-medium);
}
</style>
