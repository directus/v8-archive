<template>
  <select
    :disabled="readonly"
    class="select"
    @change="updateValue($event.target.options);"
    :id="name"
    multiple
  >
    <option v-if="options.placeholder" value="" :disabled="required">{{
      options.placeholder
    }}</option>
    <option
      v-for="(display, val) in choices"
      :key="val"
      :value="val"
      :selected="value && value.includes(val)"
      >{{ display }}</option
    >
  </select>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  computed: {
    choices() {
      let choices = this.options.choices;

      if (!choices) return {};

      if (typeof this.options.choices === "string") {
        choices = JSON.parse(this.options.choices);
      }

      return choices;
    }
  },
  methods: {
    updateValue(options) {
      let value = Array.from(options)
        .filter(input => input.selected && Boolean(input.value))
        .map(input => input.value)
        .join();

      if (value && this.options.wrapWithDelimiter) {
        value = `,${value},`;
      }

      value = value.split(",");
      this.$emit("input", value);
    }
  }
};
</script>

<style lang="scss" scoped>
.select {
  transition: all var(--fast) var(--transition);
  border: var(--input-border-width) solid var(--lighter-gray);
  border-radius: var(--border-radius);
  width: 100%;
  max-width: var(--width-large);
  font-family: "Roboto", sans-serif;
  height: 130px;

  &:hover {
    transition: none;
    border-color: var(--light-gray);
  }
  &:focus {
    border-color: var(--accent);
    option {
      color: var(--dark-gray);
    }
  }
  option {
    transition: color var(--fast) var(--transition);
    padding: 5px 10px;
    color: var(--gray);
    &:hover {
      transition: none;
      color: var(--accent);
    }
    &:checked {
      background: var(--accent)
        linear-gradient(0deg, var(--accent) 0%, var(--accent) 100%);
      position: relative;
      color: var(--white);
      -webkit-text-fill-color: var(--white);

      &::after {
        content: "check";
        font-family: "Material Icons";
        font-size: 24px;
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-54%);
      }
    }
  }
}
</style>
